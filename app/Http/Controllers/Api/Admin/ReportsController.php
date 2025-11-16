<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    /**
     * Get revenue overview
     */
    public function revenueOverview(Request $request)
    {
        $period = $request->input('period', 'month'); // day, week, month, year
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $totalRevenue = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        $totalBookings = Booking::whereBetween('created_at', [$startDate, $endDate])->count();

        $completedBookings = Booking::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $totalCommission = Booking::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('commission_amount');

        $providerRevenue = Booking::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum(DB::raw('total_amount - commission_amount'));

        // Calculate growth compared to previous period
        $periodDays = now()->parse($startDate)->diffInDays(now()->parse($endDate));
        $previousStartDate = now()->parse($startDate)->subDays($periodDays);
        $previousEndDate = now()->parse($startDate);

        $previousRevenue = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->sum('amount');

        $revenueGrowth = $previousRevenue > 0
            ? (($totalRevenue - $previousRevenue) / $previousRevenue) * 100
            : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'overview' => [
                    'total_revenue' => $totalRevenue,
                    'total_bookings' => $totalBookings,
                    'completed_bookings' => $completedBookings,
                    'total_commission' => $totalCommission,
                    'provider_revenue' => $providerRevenue,
                    'average_booking_value' => $totalBookings > 0 ? $totalRevenue / $totalBookings : 0,
                    'revenue_growth_percentage' => round($revenueGrowth, 2),
                ],
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'period_type' => $period,
                ],
            ],
        ]);
    }

    /**
     * Get revenue by period (for charts)
     */
    public function revenueByPeriod(Request $request)
    {
        $period = $request->input('period', 'month'); // day, week, month, year
        $startDate = $request->input('start_date', now()->subMonths(6));
        $endDate = $request->input('end_date', now());

        // PostgreSQL compatible date formatting - validated against whitelist
        $allowedPeriods = ['day', 'week', 'month', 'year'];
        $validatedPeriod = in_array($period, $allowedPeriods) ? $period : 'month';

        $dateFormat = match($validatedPeriod) {
            'day' => 'YYYY-MM-DD',
            'week' => 'IYYY-IW',
            'month' => 'YYYY-MM',
            'year' => 'YYYY',
            default => 'YYYY-MM',
        };

        $revenueData = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("TO_CHAR(created_at, ?) as period, SUM(amount) as total_revenue, COUNT(*) as transaction_count", [$dateFormat])
            ->groupBy('period')
            ->orderBy('period', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'revenue_data' => $revenueData,
                'period_type' => $period,
            ],
        ]);
    }

    /**
     * Get booking statistics
     */
    public function bookingStatistics(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $statusBreakdown = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        $topServices = Booking::join('booking_items', 'bookings.id', '=', 'booking_items.booking_id')
            ->join('services', 'booking_items.service_id', '=', 'services.id')
            ->whereBetween('bookings.created_at', [$startDate, $endDate])
            ->select(
                'services.id',
                'services.name_en',
                'services.name_ar',
                DB::raw('COUNT(*) as booking_count'),
                DB::raw('SUM(booking_items.quantity * booking_items.price) as total_revenue')
            )
            ->groupBy('services.id', 'services.name_en', 'services.name_ar')
            ->orderBy('booking_count', 'desc')
            ->limit(10)
            ->get();

        $topProviders = Booking::join('service_providers', 'bookings.provider_id', '=', 'service_providers.id')
            ->join('users', 'service_providers.user_id', '=', 'users.id')
            ->whereBetween('bookings.created_at', [$startDate, $endDate])
            ->select(
                'service_providers.id',
                'service_providers.business_name',
                'users.name as owner_name',
                DB::raw('COUNT(bookings.id) as booking_count'),
                DB::raw('SUM(bookings.total_amount) as total_revenue')
            )
            ->groupBy('service_providers.id', 'service_providers.business_name', 'users.name')
            ->orderBy('booking_count', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'status_breakdown' => $statusBreakdown,
                'top_services' => $topServices,
                'top_providers' => $topProviders,
            ],
        ]);
    }

    /**
     * Get provider revenue report
     */
    public function providerRevenueReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());
        $perPage = $request->input('per_page', 20);

        $providers = ServiceProvider::with('user')
            ->select('service_providers.*')
            ->addSelect([
                'total_bookings' => Booking::selectRaw('COUNT(*)')
                    ->whereColumn('bookings.provider_id', 'service_providers.id')
                    ->whereBetween('bookings.created_at', [$startDate, $endDate]),
                'completed_bookings' => Booking::selectRaw('COUNT(*)')
                    ->whereColumn('bookings.provider_id', 'service_providers.id')
                    ->where('bookings.status', 'completed')
                    ->whereBetween('bookings.created_at', [$startDate, $endDate]),
                'total_revenue' => Booking::selectRaw('COALESCE(SUM(total_amount), 0)')
                    ->whereColumn('bookings.provider_id', 'service_providers.id')
                    ->where('bookings.status', 'completed')
                    ->whereBetween('bookings.created_at', [$startDate, $endDate]),
                'total_commission' => Booking::selectRaw('COALESCE(SUM(commission_amount), 0)')
                    ->whereColumn('bookings.provider_id', 'service_providers.id')
                    ->where('bookings.status', 'completed')
                    ->whereBetween('bookings.created_at', [$startDate, $endDate]),
            ])
            ->orderByDesc('total_revenue')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'providers' => $providers->map(function ($provider) {
                    return [
                        'id' => $provider->id,
                        'business_name' => $provider->business_name,
                        'owner_name' => $provider->user->name,
                        'email' => $provider->user->email,
                        'total_bookings' => $provider->total_bookings ?? 0,
                        'completed_bookings' => $provider->completed_bookings ?? 0,
                        'total_revenue' => $provider->total_revenue ?? 0,
                        'total_commission' => $provider->total_commission ?? 0,
                        'net_revenue' => ($provider->total_revenue ?? 0) - ($provider->total_commission ?? 0),
                    ];
                }),
                'pagination' => [
                    'current_page' => $providers->currentPage(),
                    'per_page' => $providers->perPage(),
                    'total' => $providers->total(),
                    'last_page' => $providers->lastPage(),
                ],
            ],
        ]);
    }

    /**
     * Get client spending report
     */
    public function clientSpendingReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());
        $perPage = $request->input('per_page', 20);

        $clients = User::whereHas('roles', fn($q) => $q->where('name', 'client'))
            ->select('users.*')
            ->addSelect([
                'total_bookings' => Booking::selectRaw('COUNT(*)')
                    ->whereColumn('bookings.client_id', 'users.id')
                    ->whereBetween('bookings.created_at', [$startDate, $endDate]),
                'completed_bookings' => Booking::selectRaw('COUNT(*)')
                    ->whereColumn('bookings.client_id', 'users.id')
                    ->where('bookings.status', 'completed')
                    ->whereBetween('bookings.created_at', [$startDate, $endDate]),
                'total_spent' => Booking::selectRaw('COALESCE(SUM(total_amount), 0)')
                    ->whereColumn('bookings.client_id', 'users.id')
                    ->where('bookings.status', 'completed')
                    ->whereBetween('bookings.created_at', [$startDate, $endDate]),
            ])
            ->orderByDesc('total_spent')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'clients' => $clients->map(function ($client) {
                    return [
                        'id' => $client->id,
                        'name' => $client->name,
                        'email' => $client->email,
                        'phone' => $client->phone,
                        'total_bookings' => $client->total_bookings ?? 0,
                        'completed_bookings' => $client->completed_bookings ?? 0,
                        'total_spent' => $client->total_spent ?? 0,
                        'average_booking_value' => ($client->total_bookings ?? 0) > 0
                            ? ($client->total_spent ?? 0) / ($client->total_bookings ?? 0)
                            : 0,
                    ];
                }),
                'pagination' => [
                    'current_page' => $clients->currentPage(),
                    'per_page' => $clients->perPage(),
                    'total' => $clients->total(),
                    'last_page' => $clients->lastPage(),
                ],
            ],
        ]);
    }

    /**
     * Get commission report
     */
    public function commissionReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $totalCommission = Booking::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('commission_amount');

        $commissionByProvider = Booking::join('service_providers', 'bookings.provider_id', '=', 'service_providers.id')
            ->join('users', 'service_providers.user_id', '=', 'users.id')
            ->where('bookings.status', 'completed')
            ->where('bookings.total_amount', '>', 0) // Avoid division by zero
            ->whereBetween('bookings.created_at', [$startDate, $endDate])
            ->select(
                'service_providers.id',
                'service_providers.business_name',
                'users.name as owner_name',
                DB::raw('SUM(bookings.commission_amount) as total_commission'),
                DB::raw('SUM(bookings.total_amount) as total_revenue'),
                DB::raw('COUNT(bookings.id) as booking_count'),
                DB::raw('AVG(CASE WHEN bookings.total_amount > 0 THEN (bookings.commission_amount / bookings.total_amount * 100) ELSE 0 END) as avg_commission_rate')
            )
            ->groupBy('service_providers.id', 'service_providers.business_name', 'users.name')
            ->orderByDesc('total_commission')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total_commission' => $totalCommission,
                'commission_by_provider' => $commissionByProvider,
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
            ],
        ]);
    }

    /**
     * Get payment methods statistics
     */
    public function paymentMethodsStats(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $paymentStats = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                'method as payment_method',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->groupBy('method')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'payment_methods' => $paymentStats,
                'total_transactions' => $paymentStats->sum('transaction_count'),
                'total_amount' => $paymentStats->sum('total_amount'),
            ],
        ]);
    }

    /**
     * Export revenue report (placeholder)
     */
    public function exportRevenueReport(Request $request)
    {
        // TODO: Implement CSV/Excel export using Maatwebsite\Excel
        return response()->json([
            'success' => true,
            'message' => 'Export functionality coming soon',
        ]);
    }

    /**
     * Export bookings report (placeholder)
     */
    public function exportBookingsReport(Request $request)
    {
        // TODO: Implement CSV/Excel export using Maatwebsite\Excel
        return response()->json([
            'success' => true,
            'message' => 'Export functionality coming soon',
        ]);
    }
}
