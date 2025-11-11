<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\ServiceProvider;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get main dashboard KPIs and statistics
     */
    public function index(Request $request)
    {
        $period = $request->input('period', 'month'); // today, week, month, year
        $startDate = $this->getPeriodStartDate($period);
        $endDate = now();

        // Get KPIs
        $kpis = [
            'total_revenue' => Payment::where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount'),
            'total_bookings' => Booking::whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_clients' => User::whereHas('roles', fn($q) => $q->where('name', 'client'))
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'total_providers' => ServiceProvider::whereBetween('created_at', [$startDate, $endDate])->count(),
            'active_bookings' => Booking::whereIn('status', ['pending', 'confirmed', 'in_progress'])->count(),
            'completed_bookings' => Booking::where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'cancelled_bookings' => Booking::where('status', 'cancelled')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'average_rating' => round(Review::whereBetween('created_at', [$startDate, $endDate])->avg('rating'), 2),
        ];

        // Calculate growth percentages
        $previousPeriodStart = $this->getPeriodStartDate($period, 1);
        $previousPeriodEnd = $startDate;

        $previousRevenue = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])
            ->sum('amount');

        $previousBookings = Booking::whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])->count();

        $kpis['revenue_growth'] = $previousRevenue > 0
            ? round((($kpis['total_revenue'] - $previousRevenue) / $previousRevenue) * 100, 2)
            : 0;

        $kpis['bookings_growth'] = $previousBookings > 0
            ? round((($kpis['total_bookings'] - $previousBookings) / $previousBookings) * 100, 2)
            : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'kpis' => $kpis,
                'period' => [
                    'type' => $period,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
            ],
        ]);
    }

    /**
     * Get revenue chart data
     */
    public function revenueChart(Request $request)
    {
        $period = $request->input('period', 'month'); // day, week, month, year
        $days = $request->input('days', 30);

        $startDate = now()->subDays($days);
        $endDate = now();

        $dateFormat = $period === 'day' ? 'YYYY-MM-DD' : 'YYYY-MM';

        $data = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw("TO_CHAR(created_at, '{$dateFormat}') as date"),
                DB::raw('SUM(amount) as revenue'),
                DB::raw('COUNT(*) as transactions')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'chart_data' => $data,
                'period' => $period,
                'days' => $days,
            ],
        ]);
    }

    /**
     * Get bookings chart data
     */
    public function bookingsChart(Request $request)
    {
        $days = $request->input('days', 30);
        $startDate = now()->subDays($days);
        $endDate = now();

        $data = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                'status',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date', 'status')
            ->orderBy('date', 'asc')
            ->get();

        // Format data for chart
        $chartData = $data->groupBy('date')->map(function ($dayData) {
            return [
                'date' => $dayData[0]->date,
                'completed' => $dayData->where('status', 'completed')->sum('count'),
                'pending' => $dayData->where('status', 'pending')->sum('count'),
                'confirmed' => $dayData->where('status', 'confirmed')->sum('count'),
                'cancelled' => $dayData->where('status', 'cancelled')->sum('count'),
                'total' => $dayData->sum('count'),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => [
                'chart_data' => $chartData,
                'days' => $days,
            ],
        ]);
    }

    /**
     * Get users growth chart
     */
    public function usersGrowthChart(Request $request)
    {
        $days = $request->input('days', 30);
        $startDate = now()->subDays($days);
        $endDate = now();

        $clients = User::whereHas('roles', fn($q) => $q->where('name', 'client'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->pluck('count', 'date');

        $providers = ServiceProvider::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->pluck('count', 'date');

        // Merge data
        $allDates = collect($clients->keys())->merge($providers->keys())->unique()->sort()->values();

        $chartData = $allDates->map(function ($date) use ($clients, $providers) {
            return [
                'date' => $date,
                'clients' => $clients[$date] ?? 0,
                'providers' => $providers[$date] ?? 0,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'chart_data' => $chartData,
                'days' => $days,
            ],
        ]);
    }

    /**
     * Get top performing providers
     */
    public function topProviders(Request $request)
    {
        $limit = $request->input('limit', 10);
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $providers = ServiceProvider::with('user')
            ->select('service_providers.*')
            ->addSelect([
                'total_bookings' => Booking::selectRaw('COUNT(*)')
                    ->whereColumn('bookings.provider_id', 'service_providers.id')
                    ->whereBetween('bookings.created_at', [$startDate, $endDate]),
                'total_revenue' => Booking::selectRaw('COALESCE(SUM(total_amount), 0)')
                    ->whereColumn('bookings.provider_id', 'service_providers.id')
                    ->where('bookings.status', 'completed')
                    ->whereBetween('bookings.created_at', [$startDate, $endDate]),
                'average_rating' => Review::selectRaw('COALESCE(AVG(rating), 0)')
                    ->whereColumn('reviews.provider_id', 'service_providers.id'),
            ])
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => ['providers' => $providers],
        ]);
    }

    /**
     * Get recent activities
     */
    public function recentActivities(Request $request)
    {
        $limit = $request->input('limit', 20);

        $activities = [];

        // Recent bookings
        $recentBookings = Booking::with(['client', 'provider'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($booking) {
                return [
                    'type' => 'booking',
                    'action' => 'created',
                    'description' => "New booking #{$booking->booking_number}",
                    'user' => $booking->client->name ?? 'Unknown',
                    'provider' => $booking->provider->business_name ?? 'Unknown',
                    'timestamp' => $booking->created_at,
                ];
            });

        // Recent users
        $recentUsers = User::whereHas('roles', fn($q) => $q->where('name', 'client'))
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($user) {
                return [
                    'type' => 'user',
                    'action' => 'registered',
                    'description' => "New user registered",
                    'user' => $user->name,
                    'email' => $user->email,
                    'timestamp' => $user->created_at,
                ];
            });

        // Merge and sort activities
        $activities = $recentBookings->concat($recentUsers)
            ->sortByDesc('timestamp')
            ->take($limit)
            ->values();

        return response()->json([
            'success' => true,
            'data' => ['activities' => $activities],
        ]);
    }

    /**
     * Get booking status distribution
     */
    public function bookingStatusDistribution()
    {
        $distribution = Booking::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ['distribution' => $distribution],
        ]);
    }

    /**
     * Get rating distribution
     */
    public function ratingDistribution()
    {
        $distribution = Review::select('rating', DB::raw('COUNT(*) as count'))
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ['distribution' => $distribution],
        ]);
    }

    /**
     * Get comprehensive overview
     */
    public function overview()
    {
        $overview = [
            'users' => [
                'total_clients' => User::whereHas('roles', fn($q) => $q->where('name', 'client'))->count(),
                'active_clients' => User::whereHas('roles', fn($q) => $q->where('name', 'client'))
                    ->where('status', 'active')->count(),
                'total_providers' => ServiceProvider::count(),
                'verified_providers' => ServiceProvider::where('verification_status', 'approved')->count(),
            ],
            'bookings' => [
                'total' => Booking::count(),
                'pending' => Booking::where('status', 'pending')->count(),
                'confirmed' => Booking::where('status', 'confirmed')->count(),
                'completed' => Booking::where('status', 'completed')->count(),
                'cancelled' => Booking::where('status', 'cancelled')->count(),
            ],
            'revenue' => [
                'total' => Payment::where('status', 'completed')->sum('amount'),
                'this_month' => Payment::where('status', 'completed')
                    ->whereMonth('created_at', now()->month)
                    ->sum('amount'),
                'today' => Payment::where('status', 'completed')
                    ->whereDate('created_at', today())
                    ->sum('amount'),
            ],
            'reviews' => [
                'total' => Review::count(),
                'average_rating' => round(Review::avg('rating'), 2),
                'flagged' => Review::where('is_flagged', true)->count(),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $overview,
        ]);
    }

    /**
     * Helper method to get period start date
     */
    private function getPeriodStartDate($period, $periodsAgo = 0)
    {
        $date = now();

        switch ($period) {
            case 'today':
                return $date->subDays($periodsAgo)->startOfDay();
            case 'week':
                return $date->subWeeks($periodsAgo)->startOfWeek();
            case 'month':
                return $date->subMonths($periodsAgo)->startOfMonth();
            case 'year':
                return $date->subYears($periodsAgo)->startOfYear();
            default:
                return $date->subMonths($periodsAgo)->startOfMonth();
        }
    }
}
