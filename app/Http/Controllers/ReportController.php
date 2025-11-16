<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\ServiceProvider;
use App\Models\User;
use App\Models\City;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display the reports page
     */
    public function index()
    {
        // Get summary statistics
        $stats = $this->getSummaryStats();
        
        // Get all active cities
        $cities = City::active()->orderBy('name_en')->get()->map(function($city) {
            return [
                'id' => $city->id,
                'name_en' => $city->name_en,
                'name_ar' => $city->name_ar,
            ];
        })->toArray();

        return view('reports.reports', compact('stats', 'cities'));
    }

    /**
     * Get summary statistics
     */
    private function getSummaryStats()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        return [
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'total_bookings' => Booking::count(),
            'active_providers' => ServiceProvider::where('is_active', true)->where('verification_status', 'approved')->count(),
            'total_clients' => User::whereHas('roles', fn($q) => $q->where('name', 'client'))->count(),
            'today_revenue' => Payment::where('status', 'completed')->whereDate('created_at', $today)->sum('amount'),
            'today_bookings' => Booking::whereDate('created_at', $today)->count(),
            'this_month_revenue' => Payment::where('status', 'completed')->where('created_at', '>=', $thisMonth)->sum('amount'),
            'last_month_revenue' => Payment::where('status', 'completed')
                ->whereBetween('created_at', [$lastMonth, $lastMonthEnd])
                ->sum('amount'),
        ];
    }

    /**
     * Get revenue report data
     */
    public function revenueReport(Request $request)
    {
        $period = $request->input('period', 'month'); // day, week, month, year
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
        } else {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
        }

        // Revenue by day
        $revenueByDay = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as date, SUM(amount) as revenue, COUNT(*) as transactions')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Revenue by payment method
        $revenueByMethod = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('method as payment_method, SUM(amount) as revenue, COUNT(*) as transactions')
            ->groupBy('method')
            ->get();

        // Total commission
        $totalCommission = Booking::whereBetween('created_at', [$start, $end])
            ->where('status', 'completed')
            ->sum('commission_amount');

        // Total discounts
        $totalDiscounts = Booking::whereBetween('created_at', [$start, $end])
            ->sum('discount_amount');

        return response()->json([
            'success' => true,
            'data' => [
                'revenue_by_day' => $revenueByDay,
                'revenue_by_method' => $revenueByMethod,
                'total_commission' => $totalCommission,
                'total_discounts' => $totalDiscounts,
                'total_revenue' => $revenueByDay->sum('revenue'),
                'total_transactions' => $revenueByDay->sum('transactions'),
                'period' => [
                    'start' => $start->toDateString(),
                    'end' => $end->toDateString(),
                ],
            ],
        ]);
    }

    /**
     * Get booking statistics
     */
    public function bookingStats(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Bookings by status
        $bookingsByStatus = Booking::whereBetween('created_at', [$start, $end])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        // Bookings by day
        $bookingsByDay = Booking::whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, status')
            ->groupBy('date', 'status')
            ->orderBy('date')
            ->get();

        // Average booking value
        $avgBookingValue = Booking::whereBetween('created_at', [$start, $end])
            ->avg('total_amount');

        // Completion rate
        $totalBookings = Booking::whereBetween('created_at', [$start, $end])->count();
        $completedBookings = Booking::whereBetween('created_at', [$start, $end])
            ->where('status', 'completed')
            ->count();
        $completionRate = $totalBookings > 0 ? ($completedBookings / $totalBookings) * 100 : 0;

        // Cancellation rate
        $cancelledBookings = Booking::whereBetween('created_at', [$start, $end])
            ->where('status', 'cancelled')
            ->count();
        $cancellationRate = $totalBookings > 0 ? ($cancelledBookings / $totalBookings) * 100 : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'bookings_by_status' => $bookingsByStatus,
                'bookings_by_day' => $bookingsByDay,
                'avg_booking_value' => round($avgBookingValue, 2),
                'completion_rate' => round($completionRate, 2),
                'cancellation_rate' => round($cancellationRate, 2),
                'total_bookings' => $totalBookings,
                'completed_bookings' => $completedBookings,
                'cancelled_bookings' => $cancelledBookings,
            ],
        ]);
    }

    /**
     * Get provider performance report
     */
    public function providerPerformance(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth());
        $limit = $request->input('limit', 10);

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Top providers by revenue
        $topProviders = DB::table('bookings')
            ->join('service_providers', 'bookings.provider_id', '=', 'service_providers.id')
            ->whereBetween('bookings.created_at', [$start, $end])
            ->where('bookings.status', 'completed')
            ->selectRaw('
                service_providers.id,
                service_providers.business_name,
                service_providers.business_type,
                COUNT(bookings.id) as total_bookings,
                SUM(bookings.total_amount) as total_revenue,
                SUM(bookings.commission_amount) as total_commission,
                AVG(bookings.total_amount) as avg_booking_value
            ')
            ->groupBy('service_providers.id', 'service_providers.business_name', 'service_providers.business_type')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get();

        // Provider ratings
        $providerRatings = DB::table('service_providers')
            ->leftJoin('reviews', 'service_providers.id', '=', 'reviews.provider_id')
            ->selectRaw('
                service_providers.id,
                service_providers.business_name,
                AVG(reviews.rating) as avg_rating,
                COUNT(reviews.id) as total_reviews
            ')
            ->groupBy('service_providers.id', 'service_providers.business_name')
            ->orderByDesc('avg_rating')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'top_providers' => $topProviders,
                'provider_ratings' => $providerRatings,
            ],
        ]);
    }

    /**
     * Get client spending report
     */
    public function clientSpending(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth());
        $limit = $request->input('limit', 10);

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Top spending clients
        $topClients = DB::table('bookings')
            ->join('users', 'bookings.client_id', '=', 'users.id')
            ->whereBetween('bookings.created_at', [$start, $end])
            ->where('bookings.status', 'completed')
            ->selectRaw('
                users.id,
                users.name,
                users.email,
                users.phone,
                COUNT(bookings.id) as total_bookings,
                SUM(bookings.total_amount) as total_spent,
                AVG(bookings.total_amount) as avg_spent_per_booking
            ')
            ->groupBy('users.id', 'users.name', 'users.email', 'users.phone')
            ->orderByDesc('total_spent')
            ->limit($limit)
            ->get();

        // New vs returning clients
        $newClients = User::whereHas('roles', fn($q) => $q->where('name', 'client'))
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $returningClients = DB::table('bookings')
            ->select('client_id')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('client_id')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'top_clients' => $topClients,
                'new_clients' => $newClients,
                'returning_clients' => $returningClients,
            ],
        ]);
    }

    /**
     * Get commission report
     */
    public function commissionReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Commission by provider
        $commissionByProvider = DB::table('bookings')
            ->join('service_providers', 'bookings.provider_id', '=', 'service_providers.id')
            ->whereBetween('bookings.created_at', [$start, $end])
            ->where('bookings.status', 'completed')
            ->selectRaw('
                service_providers.id,
                service_providers.business_name,
                SUM(bookings.total_amount) as total_revenue,
                SUM(bookings.commission_amount) as total_commission,
                COUNT(bookings.id) as booking_count
            ')
            ->groupBy('service_providers.id', 'service_providers.business_name')
            ->orderByDesc('total_commission')
            ->get();

        // Commission by day
        $commissionByDay = Booking::whereBetween('created_at', [$start, $end])
            ->where('status', 'completed')
            ->selectRaw('DATE(created_at) as date, SUM(commission_amount) as commission')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $totalCommission = $commissionByProvider->sum('total_commission');
        $totalRevenue = $commissionByProvider->sum('total_revenue');

        return response()->json([
            'success' => true,
            'data' => [
                'commission_by_provider' => $commissionByProvider,
                'commission_by_day' => $commissionByDay,
                'total_commission' => $totalCommission,
                'total_revenue' => $totalRevenue,
                'commission_rate' => $totalRevenue > 0 ? ($totalCommission / $totalRevenue) * 100 : 0,
            ],
        ]);
    }

    /**
     * Export report to CSV/Excel
     */
    public function export(Request $request)
    {
        $type = $request->input('type', 'revenue');
        $format = $request->input('format', 'csv');
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Get data based on report type
        $data = [];
        $filename = '';
        
        switch ($type) {
            case 'revenue':
                $result = $this->getRevenueData($start, $end);
                $data = $result['data'];
                $filename = "revenue_report_{$start->format('Y-m-d')}_to_{$end->format('Y-m-d')}";
                break;
                
            case 'orders':
                $result = $this->getBookingData($start, $end);
                $data = $result['data'];
                $filename = "orders_report_{$start->format('Y-m-d')}_to_{$end->format('Y-m-d')}";
                break;
                
            case 'providers':
                $result = $this->getProviderData($start, $end);
                $data = $result['data'];
                $filename = "providers_report_{$start->format('Y-m-d')}_to_{$end->format('Y-m-d')}";
                break;
                
            case 'users':
                $result = $this->getClientData($start, $end);
                $data = $result['data'];
                $filename = "clients_report_{$start->format('Y-m-d')}_to_{$end->format('Y-m-d')}";
                break;
                
            case 'commission':
                $result = $this->getCommissionData($start, $end);
                $data = $result['data'];
                $filename = "commission_report_{$start->format('Y-m-d')}_to_{$end->format('Y-m-d')}";
                break;
        }

        // Export as CSV
        if ($format === 'csv') {
            return $this->exportAsCSV($data, $filename, $type);
        }
        
        // For Excel and PDF, show message for now
        return response()->json([
            'success' => false,
            'message' => ucfirst($format) . ' export requires additional packages. CSV export is available.',
        ]);
    }

    private function exportAsCSV($data, $filename, $type)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ];

        $callback = function() use ($data, $type) {
            $file = fopen('php://output', 'w');
            
            // Add headers based on report type
            switch ($type) {
                case 'revenue':
                    fputcsv($file, ['Date', 'Revenue (SAR)', 'Transactions', 'Avg Transaction']);
                    if (isset($data['revenue_by_day'])) {
                        foreach ($data['revenue_by_day'] as $row) {
                            fputcsv($file, [
                                $row->date,
                                number_format($row->revenue, 2),
                                $row->transactions,
                                $row->transactions > 0 ? number_format($row->revenue / $row->transactions, 2) : 0
                            ]);
                        }
                    }
                    break;
                    
                case 'providers':
                    fputcsv($file, ['Provider', 'Revenue (SAR)', 'Bookings', 'Commission (SAR)', 'Avg Booking Value']);
                    if (isset($data['top_providers'])) {
                        foreach ($data['top_providers'] as $row) {
                            fputcsv($file, [
                                $row->business_name,
                                number_format($row->total_revenue, 2),
                                $row->total_bookings,
                                number_format($row->total_commission, 2),
                                number_format($row->avg_booking_value, 2)
                            ]);
                        }
                    }
                    break;
                    
                case 'users':
                    fputcsv($file, ['Client Name', 'Email', 'Phone', 'Total Spent (SAR)', 'Bookings', 'Avg per Booking']);
                    if (isset($data['top_clients'])) {
                        foreach ($data['top_clients'] as $row) {
                            fputcsv($file, [
                                $row->name,
                                $row->email,
                                $row->phone,
                                number_format($row->total_spent, 2),
                                $row->total_bookings,
                                number_format($row->avg_spent_per_booking, 2)
                            ]);
                        }
                    }
                    break;
                    
                case 'commission':
                    fputcsv($file, ['Provider', 'Revenue (SAR)', 'Commission (SAR)', 'Bookings', 'Commission Rate (%)']);
                    if (isset($data['commission_by_provider'])) {
                        foreach ($data['commission_by_provider'] as $row) {
                            $rate = $row->total_revenue > 0 ? ($row->total_commission / $row->total_revenue) * 100 : 0;
                            fputcsv($file, [
                                $row->business_name,
                                number_format($row->total_revenue, 2),
                                number_format($row->total_commission, 2),
                                $row->booking_count,
                                number_format($rate, 2)
                            ]);
                        }
                    }
                    break;
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Helper methods to get data
    private function getRevenueData($start, $end)
    {
        $revenueByDay = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as date, SUM(amount) as revenue, COUNT(*) as transactions')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return ['data' => ['revenue_by_day' => $revenueByDay]];
    }

    private function getBookingData($start, $end)
    {
        return ['data' => []];
    }

    private function getProviderData($start, $end)
    {
        $topProviders = DB::table('bookings')
            ->join('service_providers', 'bookings.provider_id', '=', 'service_providers.id')
            ->whereBetween('bookings.created_at', [$start, $end])
            ->where('bookings.status', 'completed')
            ->selectRaw('
                service_providers.business_name,
                SUM(bookings.total_amount) as total_revenue,
                COUNT(bookings.id) as total_bookings,
                SUM(bookings.commission_amount) as total_commission,
                AVG(bookings.total_amount) as avg_booking_value
            ')
            ->groupBy('service_providers.business_name')
            ->orderByDesc('total_revenue')
            ->get();

        return ['data' => ['top_providers' => $topProviders]];
    }

    private function getClientData($start, $end)
    {
        $topClients = DB::table('bookings')
            ->join('users', 'bookings.client_id', '=', 'users.id')
            ->whereBetween('bookings.created_at', [$start, $end])
            ->where('bookings.status', 'completed')
            ->selectRaw('
                users.name,
                users.email,
                users.phone,
                SUM(bookings.total_amount) as total_spent,
                COUNT(bookings.id) as total_bookings,
                AVG(bookings.total_amount) as avg_spent_per_booking
            ')
            ->groupBy('users.name', 'users.email', 'users.phone')
            ->orderByDesc('total_spent')
            ->get();

        return ['data' => ['top_clients' => $topClients]];
    }

    private function getCommissionData($start, $end)
    {
        $commissionByProvider = DB::table('bookings')
            ->join('service_providers', 'bookings.provider_id', '=', 'service_providers.id')
            ->whereBetween('bookings.created_at', [$start, $end])
            ->where('bookings.status', 'completed')
            ->selectRaw('
                service_providers.business_name,
                SUM(bookings.total_amount) as total_revenue,
                SUM(bookings.commission_amount) as total_commission,
                COUNT(bookings.id) as booking_count
            ')
            ->groupBy('service_providers.business_name')
            ->orderByDesc('total_commission')
            ->get();

        return ['data' => ['commission_by_provider' => $commissionByProvider]];
    }
}
