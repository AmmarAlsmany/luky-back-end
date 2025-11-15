<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\ServiceProvider;
use App\Models\Service;
use App\Models\PromoCode;
use App\Models\PromoCodeUsage;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\BookingResource;
use Carbon\Carbon;

class BookingController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * Create new booking (Client)
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if ($user->user_type !== 'client') {
            throw ValidationException::withMessages([
                'message' => ['Only clients can create bookings.']
            ]);
        }

        $validated = $request->validate([
            'provider_id' => 'required|exists:service_providers,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'services' => 'required|array|min:1',
            'services.*.service_id' => 'required|exists:services,id',
            'services.*.quantity' => 'required|integer|min:1',
            'services.*.location' => 'required|in:salon,home',
            'client_address' => 'nullable|string|max:500',
            'client_latitude' => 'nullable|numeric|between:-90,90',
            'client_longitude' => 'nullable|numeric|between:-180,180',
            'notes' => 'nullable|string|max:500',
            'promo_code' => 'nullable|string|max:50',
        ]);

        // Check if any service is at home and validate client_address
        $hasHomeService = collect($validated['services'])->contains('location', 'home');
        if ($hasHomeService && empty($validated['client_address'])) {
            throw ValidationException::withMessages([
                'client_address' => ['Address is required when selecting home services.']
            ]);
        }

        $provider = ServiceProvider::with('services')->findOrFail($validated['provider_id']);

        // Validate provider is approved and active
        if ($provider->verification_status !== 'approved' || !$provider->is_active) {
            throw ValidationException::withMessages([
                'provider_id' => ['This provider is not currently accepting bookings.']
            ]);
        }

        DB::beginTransaction();
        try {
            // Calculate booking details
            $subtotal = 0;
            $totalDuration = 0;
            $serviceDetails = [];

            foreach ($validated['services'] as $serviceData) {
                $service = Service::findOrFail($serviceData['service_id']);
                
                // Validate service belongs to provider
                if ($service->provider_id !== $provider->id) {
                    throw ValidationException::withMessages([
                        'services' => ['Service does not belong to the selected provider.']
                    ]);
                }

                // Validate location availability
                if ($serviceData['location'] === 'home' && !$service->available_at_home) {
                    throw ValidationException::withMessages([
                        'services' => ["Service '{$service->name}' is not available at home."]
                    ]);
                }

                // Get correct price based on location
                $unitPrice = $service->getPriceForLocation($serviceData['location']);
                $quantity = $serviceData['quantity'];
                $itemTotal = $unitPrice * $quantity;
                
                $subtotal += $itemTotal;
                $totalDuration += ($service->duration_minutes * $quantity);

                $serviceDetails[] = [
                    'service' => $service,
                    'quantity' => $quantity,
                    'location' => $serviceData['location'],
                    'unit_price' => $unitPrice,
                    'total_price' => $itemTotal,
                ];
            }

            // Calculate end time based on total duration
            $startDateTime = $validated['booking_date'] . ' ' . $validated['start_time'];
            $endDateTime = date('Y-m-d H:i:s', strtotime($startDateTime) + ($totalDuration * 60));

            // Check if time is within working hours
            $this->validateWorkingHours($provider, $validated['booking_date'], $validated['start_time']);

            // Check concurrent bookings limit
            $this->validateConcurrentBookings($provider, $startDateTime, $endDateTime);

            // Validate and apply promo code if provided
            $discountAmount = 0;
            $promoCodeId = null;

            if (!empty($validated['promo_code'])) {
                $promoCode = PromoCode::where('code', strtoupper($validated['promo_code']))->first();

                if (!$promoCode) {
                    throw ValidationException::withMessages([
                        'promo_code' => ['Invalid promo code']
                    ]);
                }

                // Validate promo code
                if (!$promoCode->isValid()) {
                    throw ValidationException::withMessages([
                        'promo_code' => ['This promo code has expired or is no longer active']
                    ]);
                }

                // Check if user can use this promo code
                if (!$promoCode->canBeUsedByUser($user->id)) {
                    throw ValidationException::withMessages([
                        'promo_code' => ['You have already used this promo code the maximum number of times']
                    ]);
                }

                // Check minimum booking amount
                if ($promoCode->min_booking_amount && $subtotal < $promoCode->min_booking_amount) {
                    throw ValidationException::withMessages([
                        'promo_code' => ["Minimum booking amount of {$promoCode->min_booking_amount} SAR required for this promo code"]
                    ]);
                }

                // Check service applicability
                if ($promoCode->applicable_services) {
                    $applicableServiceIds = $promoCode->applicable_services;
                    $bookingServiceIds = collect($validated['services'])->pluck('service_id')->toArray();
                    $hasApplicableService = !empty(array_intersect($applicableServiceIds, $bookingServiceIds));

                    if (!$hasApplicableService) {
                        throw ValidationException::withMessages([
                            'promo_code' => ['This promo code is not applicable to the selected services']
                        ]);
                    }
                }

                // Calculate discount
                $discountAmount = $promoCode->calculateDiscount($subtotal);
                $promoCodeId = $promoCode->id;
            }

            // Calculate tax and commission (after discount)
            $amountAfterDiscount = $subtotal - $discountAmount;
            $taxRate = 0.15; // 15% VAT
            $taxAmount = $amountAfterDiscount * $taxRate;
            $totalAmount = $amountAfterDiscount + $taxAmount;
            $commissionAmount = $amountAfterDiscount * ($provider->commission_rate / 100);

            // Generate unique booking number with timestamp and random component
            $bookingNumber = 'BK' . date('Ymd') . strtoupper(substr(uniqid(), -6));

            // Create booking
            $booking = Booking::create([
                'booking_number' => $bookingNumber,
                'client_id' => $user->id,
                'provider_id' => $provider->id,
                'booking_date' => $validated['booking_date'],
                'start_time' => $startDateTime,
                'end_time' => $endDateTime,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'commission_amount' => $commissionAmount,
                'payment_status' => 'pending',
                'notes' => $validated['notes'] ?? null,
                'client_address' => $validated['client_address'] ?? null,
                'client_latitude' => $validated['client_latitude'] ?? null,
                'client_longitude' => $validated['client_longitude'] ?? null,
                'promo_code_id' => $promoCodeId,
            ]);

            // Create booking items
            foreach ($serviceDetails as $detail) {
                BookingItem::create([
                    'booking_id' => $booking->id,
                    'service_id' => $detail['service']->id,
                    'quantity' => $detail['quantity'],
                    'unit_price' => $detail['unit_price'],
                    'total_price' => $detail['total_price'],
                    'service_location' => $detail['location'],
                ]);
            }

            // Record promo code usage if applied
            if ($promoCodeId) {
                PromoCodeUsage::create([
                    'promo_code_id' => $promoCodeId,
                    'user_id' => $user->id,
                    'booking_id' => $booking->id,
                    'discount_amount' => $discountAmount,
                ]);

                // Increment promo code used count
                PromoCode::where('id', $promoCodeId)->increment('used_count');
            }

            DB::commit();

            // Send notification to provider about new booking request
            $this->notificationService->sendBookingRequest($booking);

            return response()->json([
                'success' => true,
                'message' => 'Booking request submitted successfully',
                'data' => new BookingResource($booking->load(['client', 'provider', 'items.service']))
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Validate working hours
     */
    protected function validateWorkingHours(ServiceProvider $provider, string $date, string $time)
    {
        $dayOfWeek = strtolower(date('l', strtotime($date)));
        
        // Check if day is off
        if ($provider->off_days && in_array($dayOfWeek, $provider->off_days)) {
            throw ValidationException::withMessages([
                'booking_date' => ['Provider is not available on this day.']
            ]);
        }

        // Check working hours
        if ($provider->working_hours && isset($provider->working_hours[$dayOfWeek])) {
            $workingHours = $provider->working_hours[$dayOfWeek];

            if ($time < $workingHours['open'] || $time > $workingHours['close']) {
                throw ValidationException::withMessages([
                    'start_time' => ['Time is outside provider working hours.']
                ]);
            }
        }
    }

    /**
     * Validate concurrent bookings
     */
    protected function validateConcurrentBookings(ServiceProvider $provider, string $startTime, string $endTime)
    {
        $concurrentCount = Booking::where('provider_id', $provider->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                      ->orWhereBetween('end_time', [$startTime, $endTime])
                      ->orWhere(function ($q) use ($startTime, $endTime) {
                          $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                      });
            })
            ->count();

        if ($concurrentCount >= $provider->max_concurrent_bookings) {
            throw ValidationException::withMessages([
                'start_time' => ['No available slots at this time. Please choose another time.']
            ]);
        }
    }

    /**
     * Get client bookings
     */
    public function clientBookings(Request $request): JsonResponse
    {
        $user = $request->user();
        $status = $request->get('status'); // pending, confirmed, completed, cancelled

        $query = Booking::with(['provider.user', 'items.service'])
            ->where('client_id', $user->id);

        if ($status) {
            $query->where('status', $status);
        }

        $bookings = $query->orderByDesc('created_at')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => BookingResource::collection($bookings->items()),
            'pagination' => [
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'total' => $bookings->total(),
            ]
        ]);
    }

    /**
     * Get booking details by ID
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $booking = Booking::with(['provider.user', 'items.service', 'client'])
            ->findOrFail($id);

        // Verify user has access to this booking
        if ($booking->client_id !== $user->id &&
            ($user->user_type !== 'provider' || $booking->provider->user_id !== $user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to booking.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => new BookingResource($booking)
        ]);
    }

    /**
     * Get provider bookings
     */
    public function providerBookings(Request $request): JsonResponse
    {
        $user = $request->user();
        $provider = $user->providerProfile;

        if (!$provider) {
            throw ValidationException::withMessages([
                'message' => ['Provider profile not found.']
            ]);
        }

        $status = $request->get('status');
        $date = $request->get('date'); // YYYY-MM-DD

        $query = Booking::with(['client', 'items.service'])
            ->where('provider_id', $provider->id);

        if ($status) {
            $query->where('status', $status);
        }

        if ($date) {
            $query->whereDate('booking_date', $date);
        }

        $bookings = $query->orderBy('start_time')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => BookingResource::collection($bookings->items()),
            'pagination' => [
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'total' => $bookings->total(),
            ]
        ]);
    }

    /**
     * Accept booking (Provider)
     */
    public function acceptBooking(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $provider = $user->providerProfile;

        $booking = Booking::where('provider_id', $provider->id)
            ->where('id', $id)
            ->where('status', 'pending')
            ->firstOrFail();

        $booking->update([
            'status' => 'confirmed',
            'confirmed_at' => now()
        ]);

        // Send notification to client about booking acceptance
        $this->notificationService->sendBookingAccepted($booking);

        return response()->json([
            'success' => true,
            'message' => 'Booking accepted successfully',
            'data' => new BookingResource($booking->load(['client', 'items.service']))
        ]);
    }

    /**
     * Reject booking (Provider)
     */
    public function rejectBooking(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $provider = $user->providerProfile;

        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:500'
        ]);

        $booking = Booking::where('provider_id', $provider->id)
            ->where('id', $id)
            ->where('status', 'pending')
            ->firstOrFail();

        $booking->update([
            'status' => 'rejected',
            'cancellation_reason' => $validated['rejection_reason'] ?? 'Rejected by provider',
            'cancelled_by' => 'provider',
            'cancelled_at' => now()
        ]);

        // Send notification to client about booking rejection
        $this->notificationService->sendBookingRejected($booking);

        return response()->json([
            'success' => true,
            'message' => 'Booking rejected',
            'data' => new BookingResource($booking->load(['client', 'items.service']))
        ]);
    }

    /**
     * Calculate cancellation fee based on time remaining before appointment
     */
    private function calculateCancellationFee(Booking $booking): array
    {
        // If not paid yet, no fee
        if ($booking->payment_status !== 'paid') {
            return [
                'fee' => 0,
                'refund' => 0,
                'percentage' => 0,
                'reason' => 'No payment made yet'
            ];
        }

        // Calculate hours until appointment
        // Use start_time if available, otherwise fall back to booking_time
        $timeField = $booking->start_time ?? $booking->booking_time;
        if (!$timeField) {
            // If no time is set, assume appointment is in the future
            $appointmentDateTime = \Carbon\Carbon::parse($booking->booking_date)->endOfDay();
        } else {
            $appointmentDateTime = \Carbon\Carbon::parse($timeField);
        }
        $hoursUntilAppointment = now()->diffInHours($appointmentDateTime, false);

        // If appointment already passed, no refund
        if ($hoursUntilAppointment < 0) {
            return [
                'fee' => $booking->total_amount,
                'refund' => 0,
                'percentage' => 100,
                'reason' => 'Appointment time has passed'
            ];
        }

        // Time-based cancellation policy
        $feePercentage = 0;
        $reason = '';

        if ($hoursUntilAppointment < 24) {
            // Less than 24 hours: 50% fee
            $feePercentage = 50;
            $reason = 'Cancellation within 24 hours of appointment';
        } elseif ($hoursUntilAppointment < 48) {
            // 24-48 hours: 25% fee
            $feePercentage = 25;
            $reason = 'Cancellation within 48 hours of appointment';
        } else {
            // More than 48 hours: No fee
            $feePercentage = 0;
            $reason = 'Free cancellation (more than 48 hours notice)';
        }

        $cancellationFee = ($booking->total_amount * $feePercentage) / 100;
        $refundAmount = $booking->total_amount - $cancellationFee;

        return [
            'fee' => round($cancellationFee, 2),
            'refund' => round($refundAmount, 2),
            'percentage' => $feePercentage,
            'reason' => $reason,
            'hours_until_appointment' => round($hoursUntilAppointment, 1)
        ];
    }

    /**
     * Preview cancellation fees before actually cancelling
     */
    public function previewCancellation(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $booking = Booking::where('client_id', $user->id)
            ->where('id', $id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->firstOrFail();

        $feeInfo = $this->calculateCancellationFee($booking);

        return response()->json([
            'success' => true,
            'data' => [
                'booking_id' => $booking->id,
                'total_amount' => (float) $booking->total_amount,
                'cancellation_fee' => (float) $feeInfo['fee'],
                'refund_amount' => (float) $feeInfo['refund'],
                'fee_percentage' => $feeInfo['percentage'],
                'reason' => $feeInfo['reason'],
                'hours_until_appointment' => $feeInfo['hours_until_appointment'] ?? null,
                'appointment_date' => $booking->booking_date,
                'appointment_time' => $booking->start_time ?? $booking->booking_time,
            ]
        ]);
    }

    /**
     * Cancel booking (Client)
     */
    public function cancelBooking(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'cancellation_reason' => 'nullable|string|max:500'
        ]);

        $booking = Booking::with('payment')->where('client_id', $user->id)
            ->where('id', $id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->firstOrFail();

        DB::beginTransaction();
        try {
            // Calculate cancellation fee based on time remaining
            $feeInfo = $this->calculateCancellationFee($booking);
            $cancellationFee = $feeInfo['fee'];
            $refundAmount = $feeInfo['refund'];

            // Update payment status if refund is applicable
            if ($refundAmount > 0 && $booking->payment_status === 'paid') {
                $booking->update(['payment_status' => 'refunded']);
                // TODO: Process actual refund via MyFatoorah API
            }

            $booking->update([
                'status' => 'cancelled',
                'cancellation_reason' => $validated['cancellation_reason'] ?? 'Cancelled by client',
                'cancelled_by' => 'client',
                'cancelled_at' => now(),
                'discount_amount' => $booking->discount_amount + $cancellationFee, // Store cancellation fee
            ]);

            DB::commit();

            $message = 'Booking cancelled successfully';
            if ($refundAmount > 0) {
                $message .= sprintf('. Refund of %.2f SAR will be processed (cancellation fee: %.2f SAR)', $refundAmount, $cancellationFee);
                // Send refund notification to client
                $this->notificationService->sendRefundProcessed($booking, $refundAmount, $cancellationFee);
            }

            // Send cancellation notification to provider
            $this->notificationService->sendBookingCancelled($booking, 'provider');

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'booking' => new BookingResource($booking->load(['provider', 'items.service'])),
                    'refund_info' => [
                        'cancellation_fee' => (float) $cancellationFee,
                        'refund_amount' => (float) $refundAmount,
                        'total_paid' => (float) $booking->total_amount,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get provider daily schedule
     */
    public function providerSchedule(Request $request): JsonResponse
    {
        $user = $request->user();
        $provider = $user->providerProfile;

        $date = $request->get('date', now()->format('Y-m-d'));

        $bookings = Booking::with(['client', 'items.service'])
            ->where('provider_id', $provider->id)
            ->whereDate('booking_date', $date)
            ->whereIn('status', ['pending', 'confirmed', 'completed'])
            ->orderBy('start_time')
            ->get();

        // Get concurrent bookings info
        $timeSlots = [];
        foreach ($bookings as $booking) {
            $startHour = date('H:i', strtotime($booking->start_time));
            
            if (!isset($timeSlots[$startHour])) {
                $timeSlots[$startHour] = [
                    'time' => $startHour,
                    'bookings_count' => 0,
                    'max_concurrent' => $provider->max_concurrent_bookings,
                    'available_slots' => $provider->max_concurrent_bookings,
                    'bookings' => []
                ];
            }
            
            $timeSlots[$startHour]['bookings_count']++;
            $timeSlots[$startHour]['available_slots'] = max(0, $provider->max_concurrent_bookings - $timeSlots[$startHour]['bookings_count']);
            $timeSlots[$startHour]['bookings'][] = new BookingResource($booking);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'date' => $date,
                'total_bookings' => $bookings->count(),
                'max_concurrent_bookings' => $provider->max_concurrent_bookings,
                'schedule' => array_values($timeSlots),
                'all_bookings' => BookingResource::collection($bookings)
            ]
        ]);
    }
}