<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\City;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }
    /**
     * Display a listing of clients
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $search = $request->input('search');
        $status = $request->input('status');
        $cityId = $request->input('city_id');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = User::whereHas('roles', function ($q) {
            $q->where('name', 'client');
        })
        ->with(['city', 'roles'])
        ->withCount('bookings as bookings_count')
        ->withSum(['bookings as total_spent' => function($query) {
            $query->where('status', 'completed');
        }], 'total_amount');

        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        // Status filter
        if ($status) {
            $query->where('status', $status);
        }

        // City filter
        if ($cityId) {
            $query->where('city_id', $cityId);
        }

        // Date range filter
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Sorting
        $query->orderBy($sortBy, $sortOrder);

        $clients = $query->paginate($perPage);

        // Transform clients data
        $clientsData = $clients->map(function ($client) {
            return [
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email,
                'phone' => $client->phone,
                'city' => $client->city ? (app()->getLocale() === 'ar' ? $client->city->name_ar : $client->city->name_en) : null,
                'city_id' => $client->city_id,
                'status' => $client->status,
                'bookings_count' => $client->bookings_count ?? 0,
                'total_spent' => $client->total_spent ?? 0,
                'avatar' => $client->avatar,
                'avatar_url' => $client->avatar,
                'created_at' => $client->created_at,
                'last_login_at' => $client->last_login_at,
            ];
        });

        // Get stats
        $stats = [
            'total_clients' => User::whereHas('roles', fn($q) => $q->where('name', 'client'))->count(),
            'active_clients' => User::whereHas('roles', fn($q) => $q->where('name', 'client'))
                ->where('status', 'active')->count(),
            'inactive_clients' => User::whereHas('roles', fn($q) => $q->where('name', 'client'))
                ->where('status', 'inactive')->count(),
            'new_clients' => User::whereHas('roles', fn($q) => $q->where('name', 'client'))
                ->whereDate('created_at', '>=', now()->subDays(30))->count(),
        ];

        // Get cities for filter
        $cities = City::select('id', 'name_en', 'name_ar')->get()->map(function($city) {
            return [
                'id' => $city->id,
                'name' => app()->getLocale() === 'ar' ? $city->name_ar : $city->name_en,
                'name_en' => $city->name_en,
                'name_ar' => $city->name_ar,
            ];
        });

        $pagination = [
            'current_page' => $clients->currentPage(),
            'last_page' => $clients->lastPage(),
            'per_page' => $clients->perPage(),
            'total' => $clients->total(),
            'from' => $clients->firstItem(),
            'to' => $clients->lastItem(),
        ];

        $filters = compact('search', 'status', 'cityId', 'dateFrom', 'dateTo');

        return view('clients.list', [
            'clients' => $clientsData,
            'pagination' => $pagination,
            'stats' => $stats,
            'cities' => $cities,
            'filters' => $filters
        ]);
    }

    /**
     * Display the specified client
     */
    public function show($id)
    {
        $client = User::whereHas('roles', function ($q) {
            $q->where('name', 'client');
        })->with(['city'])->findOrFail($id);

        // Get client bookings
        $bookings = Booking::where('client_id', $id)
            ->with(['provider.user', 'items.service'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Get stats
        $totalBookings = Booking::where('client_id', $id)->count();
        $completedBookings = Booking::where('client_id', $id)->where('status', 'completed')->count();
        $cancelledBookings = Booking::where('client_id', $id)->where('status', 'cancelled')->count();
        $totalSpent = Booking::where('client_id', $id)
            ->where('status', 'completed')
            ->sum('total_amount');

        $clientData = [
            'id' => $client->id,
            'name' => $client->name,
            'email' => $client->email,
            'phone' => $client->phone,
            'gender' => $client->gender,
            'date_of_birth' => $client->date_of_birth ? $client->date_of_birth->format('Y-m-d') : null,
            'city' => $client->city,
            'city_id' => $client->city_id,
            'status' => $client->status,
            'avatar_url' => $client->avatar,
            'created_at' => $client->created_at,
            'last_login_at' => $client->last_login_at,
            'total_bookings' => $totalBookings,
            'completed_bookings' => $completedBookings,
            'cancelled_bookings' => $cancelledBookings,
            'total_spent' => $totalSpent,
            'bookings' => $bookings,
        ];

        return view('clients.details', ['client' => $clientData]);
    }

    /**
     * Create new client
     */
    public function store(Request $request)
    {
        // Normalize phone number before validation
        $phoneService = new \App\Services\PhoneNumberService();
        $normalizedPhone = $phoneService->normalize($request->input('phone'));

        // Check if phone already exists (with normalized format)
        $existingPhone = User::where('phone', $normalizedPhone)->exists();
        if ($existingPhone) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => [
                    'phone' => ['This phone number is already registered in the system.']
                ]
            ], 422);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string',
            'city_id' => 'nullable|exists:cities,id',
            'password' => 'nullable|string|min:8',
        ], [
            'email.unique' => 'This email address is already registered in the system.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'phone.required' => 'Phone number is required.',
            'name.required' => 'Client name is required.',
            'password.min' => 'Password must be at least 8 characters.',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'city_id' => $validated['city_id'] ?? null,
                'user_type' => 'client',
                'status' => 'active',
                'password' => isset($validated['password']) ? Hash::make($validated['password']) : null,
            ]);

            $user->assignRole('client');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Client created successfully',
                'data' => ['client' => $user]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Check if it's a unique constraint violation
            if (str_contains($e->getMessage(), 'user_phone_uniq') || str_contains($e->getMessage(), 'phone')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation Error',
                    'errors' => [
                        'phone' => ['This phone number is already registered in the system.']
                    ]
                ], 422);
            }
            
            if (str_contains($e->getMessage(), 'user_email_uniq') || str_contains($e->getMessage(), 'email')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation Error',
                    'errors' => [
                        'email' => ['This email address is already registered in the system.']
                    ]
                ], 422);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create client: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update client
     */
    public function update(Request $request, $id)
    {
        $client = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'phone' => 'sometimes|required|string|unique:users,phone,' . $id,
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date|before:today',
            'city_id' => 'nullable|exists:cities,id',
            'status' => 'sometimes|required|in:active,inactive,suspended',
        ]);

        $client->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Client updated successfully',
            'data' => ['client' => $client]
        ]);
    }

    /**
     * Update client status
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $client = User::findOrFail($id);
        $client->update([
            'status' => $validated['status'],
            'is_active' => $validated['status'] === 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Client status updated successfully',
        ]);
    }

    /**
     * Delete client
     */
    public function destroy($id)
    {
        $client = User::findOrFail($id);
        $client->delete();

        return response()->json([
            'success' => true,
            'message' => 'Client deleted successfully',
        ]);
    }

    /**
     * Get client bookings
     */
    public function bookings($id)
    {
        $bookings = Booking::where('client_id', $id)
            ->with(['provider.user', 'items.service'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ['bookings' => $bookings]
        ]);
    }

    /**
     * Get client transactions
     */
    public function transactions($id)
    {
        // Get payments through bookings (payments are linked to bookings, not directly to users)
        $transactions = Payment::whereHas('booking', function($query) use ($id) {
                $query->where('client_id', $id);
            })
            ->with(['booking' => function($query) {
                $query->select('id', 'booking_number', 'client_id', 'total_amount', 'status');
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ['transactions' => $transactions]
        ]);
    }

    /**
     * Send notification to client
     */
    public function sendNotification(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:500',
        ]);

        $client = User::findOrFail($id);

        try {
            // Create notification in database
            \App\Models\Notification::create([
                'user_id' => $client->id,
                'type' => 'admin_message',
                'title' => $validated['title'],
                'body' => $validated['message'],
                'data' => [
                    'sent_by' => auth()->user()->name ?? 'Admin',
                    'sent_at' => now()->toDateTimeString(),
                ],
                'is_read' => false,
                'is_sent' => true,
            ]);

            // TODO: Send push notification via FCM/Firebase if token exists
            // if ($client->fcm_token) {
            //     SendPushNotification::dispatch($client->fcm_token, $validated);
            // }

            // Return JSON for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification sent successfully to ' . $client->name
                ]);
            }

            return redirect()->back()->with('success', 'Notification sent successfully to ' . $client->name);
        } catch (\Exception $e) {
            // Return JSON error for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send notification: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to send notification: ' . $e->getMessage());
        }
    }

    /**
     * Send SMS message to client
     */
    public function sendMessage(Request $request, $id)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $client = User::findOrFail($id);

        try {
            // Send SMS
            $smsSent = false;
            try {
                $smsSent = $this->smsService->send($client->phone, $validated['message']);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('SMS send failed: ' . $e->getMessage());
            }

            // Create notification record
            $notification = \App\Models\Notification::create([
                'user_id' => $client->id,
                'type' => 'sms',
                'title' => 'SMS Message',
                'body' => $validated['message'],
                'data' => [
                    'phone' => $client->phone,
                    'sent_by' => auth()->user()->name ?? 'Admin',
                    'sent_at' => now()->toDateTimeString(),
                ],
                'is_read' => false,
                'is_sent' => $smsSent,
            ]);

            $message = $smsSent 
                ? 'SMS sent successfully to ' . $client->phone
                : 'Message queued to be sent to ' . $client->phone;

            // Return JSON for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'sms_sent' => $smsSent
                ]);
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            // Return JSON error for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send message: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to send SMS: ' . $e->getMessage());
        }
    }

    /**
     * Export clients to CSV
     */
    public function export(Request $request)
    {
        $clients = User::whereHas('roles', function ($q) {
            $q->where('name', 'client');
        })
        ->with(['city'])
        ->withCount('bookings as bookings_count')
        ->withSum(['bookings as total_spent' => function($query) {
            $query->where('status', 'completed');
        }], 'total_amount')
        ->get();

        $filename = 'clients_export_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w');

        // CSV Headers
        fputcsv($handle, [
            'ID',
            'Name',
            'Email',
            'Phone',
            'City',
            'Status',
            'Total Bookings',
            'Total Spent (SAR)',
            'Registered Date',
            'Last Login'
        ]);

        // Data rows
        foreach ($clients as $client) {
            fputcsv($handle, [
                $client->id,
                $client->name,
                $client->email,
                $client->phone,
                $client->city ? $client->city->name_en : 'N/A',
                $client->status,
                $client->bookings_count ?? 0,
                number_format($client->total_spent ?? 0, 2),
                $client->created_at->format('Y-m-d H:i:s'),
                $client->last_login_at ? $client->last_login_at->format('Y-m-d H:i:s') : 'Never'
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
