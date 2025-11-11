<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Booking;
use App\Models\ServiceProvider;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard
     */
    public function admin(Request $request)
    {
        // Admin dashboard doesn't need to load data from backend
        // All data will be loaded via AJAX calls to the API
        return view('dashboards.admin');
    }
    
    /**
     * Display the main dashboard
     */
    public function index(Request $request)
    {
        $period = $request->get('period', 'month');

        // Get date range based on period
        $dateRange = $this->getDateRange($period);

        // Get overview stats
        $overview = [
            'total_revenue' => Booking::where('status', 'completed')
                ->whereBetween('created_at', $dateRange)
                ->sum('total_amount'),
            'commission_earned' => Booking::where('status', 'completed')
                ->whereBetween('created_at', $dateRange)
                ->sum('commission_amount') ?? 0,
            'total_bookings' => Booking::whereBetween('created_at', $dateRange)->count(),
            'total_clients' => User::whereHas('roles', fn($q) => $q->where('name', 'client'))
                ->whereBetween('created_at', $dateRange)
                ->count(),
            'total_providers' => ServiceProvider::whereBetween('created_at', $dateRange)->count(),
            'active_providers' => ServiceProvider::where('is_active', true)->count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'completed_bookings' => Booking::where('status', 'completed')
                ->whereBetween('created_at', $dateRange)
                ->count(),
        ];

        // Calculate growth percentages compared to previous period
        $previousDateRange = $this->getPreviousDateRange($period);

        $previousRevenue = Booking::where('status', 'completed')
            ->whereBetween('created_at', $previousDateRange)
            ->sum('total_amount');

        $previousCommission = Booking::where('status', 'completed')
            ->whereBetween('created_at', $previousDateRange)
            ->sum('commission_amount') ?? 0;

        $previousBookings = Booking::whereBetween('created_at', $previousDateRange)->count();

        $previousClients = User::whereHas('roles', fn($q) => $q->where('name', 'client'))
            ->whereBetween('created_at', $previousDateRange)
            ->count();

        $previousProviders = ServiceProvider::whereBetween('created_at', $previousDateRange)->count();

        $comparisons = [
            'revenue_change' => $this->calculateGrowth($overview['total_revenue'], $previousRevenue),
            'commission_change' => $this->calculateGrowth($overview['commission_earned'], $previousCommission),
            'bookings_change' => $this->calculateGrowth($overview['total_bookings'], $previousBookings),
            'clients_change' => $this->calculateGrowth($overview['total_clients'], $previousClients),
            'providers_change' => $this->calculateGrowth($overview['total_providers'], $previousProviders),
        ];

        // Get revenue trends
        $revenueTrend = $this->getRevenueTrend($period);

        // Get booking trends
        $bookingsTrend = $this->getBookingsTrend($period);

        // Get top providers
        $topProviders = $this->getTopProviders();

        // Get top client
        $topClient = User::whereHas('roles', fn($q) => $q->where('name', 'client'))
            ->withSum(['bookings as total_spent' => function($query) {
                $query->where('status', 'completed');
            }], 'total_amount')
            ->orderBy('total_spent', 'desc')
            ->first();

        $topClient = $topClient ? [
            'name' => $topClient->name,
            'avatar_url' => $topClient->avatar,
            'total_spent' => $topClient->total_spent ?? 0,
        ] : null;

        // Get recent activities
        $recentActivities = $this->getRecentActivities();

        // Providers by region (placeholder)
        $providersByRegion = [];

        return view('dashboards.index', compact(
            'overview',
            'comparisons',
            'revenueTrend',
            'bookingsTrend',
            'topProviders',
            'topClient',
            'recentActivities',
            'providersByRegion',
            'period'
        ));
    }

    /**
     * Get revenue chart data
     */
    public function getRevenueChart(Request $request)
    {
        $period = $request->get('period', 'month');
        $data = $this->getRevenueTrend($period);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get bookings chart data
     */
    public function getBookingsChart(Request $request)
    {
        $period = $request->get('period', 'month');
        $data = $this->getBookingsTrend($period);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get top providers data
     */
    public function getTopProvidersApi(Request $request)
    {
        $data = $this->getTopProviders();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get recent activities
     */
    public function getRecentActivitiesApi(Request $request)
    {
        $data = $this->getRecentActivities();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Helper: Get date range based on period
     */
    private function getDateRange($period)
    {
        switch ($period) {
            case 'today':
                return [Carbon::today(), Carbon::tomorrow()];
            case 'week':
                return [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()];
            case 'month':
                return [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
            case 'year':
                return [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()];
            default:
                return [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
        }
    }

    /**
     * Helper: Get revenue trend data
     */
    private function getRevenueTrend($period)
    {
        $dateRange = $this->getDateRange($period);

        $revenue = Booking::where('status', 'completed')
            ->whereBetween('created_at', $dateRange)
            ->selectRaw("to_char(created_at, 'YYYY-MM-DD') as date, SUM(total_amount) as total")
            ->groupBy(DB::raw("to_char(created_at, 'YYYY-MM-DD')"))
            ->orderByRaw("to_char(created_at, 'YYYY-MM-DD')")
            ->get();

        return [
            'labels' => $revenue->pluck('date')->map(fn($date) => Carbon::createFromFormat('Y-m-d', $date)->format('M d')),
            'data' => $revenue->pluck('total'),
        ];
    }

    /**
     * Helper: Get bookings trend data
     */
    private function getBookingsTrend($period)
    {
        $dateRange = $this->getDateRange($period);

        $bookings = Booking::whereBetween('created_at', $dateRange)
            ->selectRaw("to_char(created_at, 'YYYY-MM-DD') as date, status, COUNT(*) as count")
            ->groupBy(DB::raw("to_char(created_at, 'YYYY-MM-DD')"), 'status')
            ->orderByRaw("to_char(created_at, 'YYYY-MM-DD')")
            ->get();

        $dateKeys = $bookings->pluck('date')->unique()->values();
        $labels = $dateKeys->map(fn($date) => Carbon::createFromFormat('Y-m-d', $date)->format('M d'));

        $statuses = ['pending', 'confirmed', 'completed', 'cancelled', 'rejected'];
        $datasets = [];

        foreach ($statuses as $status) {
            $data = $dateKeys->map(function($dateKey) use ($bookings, $status) {
                $booking = $bookings->firstWhere(function($b) use ($dateKey, $status) {
                    return $b->date === $dateKey && $b->status === $status;
                });
                return $booking ? $booking->count : 0;
            });

            $datasets[] = [
                'label' => ucfirst($status),
                'data' => $data->values(),
            ];
        }

        return [
            'labels' => $labels->values(),
            'datasets' => $datasets,
        ];
    }

    /**
     * Helper: Get top providers
     */
    private function getTopProviders()
    {
        return ServiceProvider::with(['user', 'city'])
            ->withCount(['bookings as completed_bookings_count' => function($query) {
                $query->where('status', 'completed');
            }])
            ->withSum(['bookings as total_revenue' => function($query) {
                $query->where('status', 'completed');
            }], 'total_amount')
            ->where('is_active', true)
            ->orderBy('completed_bookings_count', 'desc')
            ->take(10)
            ->get()
            ->map(function($provider) {
                return [
                    'id' => $provider->id,
                    'name' => $provider->user->name ?? 'N/A',
                    'business_name' => $provider->business_name,
                    'business_type' => $provider->business_type ?? 'individual',
                    'city_id' => $provider->city_id,
                    'avatar' => $provider->user->avatar ?? null,
                    'total_bookings' => $provider->completed_bookings_count ?? 0,
                    'total_revenue' => $provider->total_revenue ?? 0,
                    'revenue' => $provider->total_revenue ?? 0,
                    'rating' => $provider->average_rating ?? 0,
                ];
            })
            ->toArray();
    }

    /**
     * Helper: Get recent activities
     */
    private function getRecentActivities()
    {
        $bookings = Booking::with(['client', 'provider.user'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function($booking) {
                $provider = $booking->provider;
                $providerName = $provider ? ($provider->business_name ?? $provider->user->name ?? 'N/A') : 'N/A';

                return [
                    'type' => 'booking',
                    'title' => 'New booking created',
                    'description' => ($booking->client->name ?? 'Client') . ' booked with ' . $providerName,
                    'status' => $booking->status,
                    'time' => $booking->created_at->diffForHumans(),
                    'created_at' => $booking->created_at,
                ];
            })
            ->toArray();

        return $bookings;
    }

    /**
     * Helper: Get previous period date range
     */
    private function getPreviousDateRange($period)
    {
        switch ($period) {
            case 'today':
                return [Carbon::yesterday()->startOfDay(), Carbon::today()->startOfDay()];
            case 'week':
                return [
                    Carbon::now()->subWeek()->startOfWeek(),
                    Carbon::now()->subWeek()->endOfWeek()
                ];
            case 'month':
                return [
                    Carbon::now()->subMonth()->startOfMonth(),
                    Carbon::now()->subMonth()->endOfMonth()
                ];
            case 'year':
                return [
                    Carbon::now()->subYear()->startOfYear(),
                    Carbon::now()->subYear()->endOfYear()
                ];
            default:
                return [
                    Carbon::now()->subMonth()->startOfMonth(),
                    Carbon::now()->subMonth()->endOfMonth()
                ];
        }
    }

    /**
     * Helper: Calculate growth percentage
     */
    private function calculateGrowth($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? '+100%' : '0%';
        }

        $growth = (($current - $previous) / $previous) * 100;
        $sign = $growth >= 0 ? '+' : '';

        return $sign . number_format($growth, 1) . '%';
    }
}
