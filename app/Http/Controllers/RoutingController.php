<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\ServiceProvider;
use App\Models\ProviderPaymentSetting;

class RoutingController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Display a listing of the resource.
     * Home page - redirect to dashboard if authenticated, otherwise to login
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Check if authenticated
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if user is a dashboard user
            $dashboardRoles = ['super_admin', 'admin', 'manager', 'support_agent', 'content_manager', 'analyst'];
            if ($user->hasAnyRole($dashboardRoles)) {
                // Super admin and admin go to admin dashboard
                if ($user->hasRole(['admin', 'super_admin'])) {
                    return redirect('/dashboards/admin');
                } else {
                    return redirect('/dashboards/index');
                }
            } else {
                // Non-dashboard users should logout
                Auth::logout();
                return redirect('/login')->with('error', 'You do not have permission to access the admin panel.');
            }
        } else {
            return redirect('/login');
        }
    }

    /**
     * Display a view based on first route param
     *
     * @return \Illuminate\Http\Response
     */
    public function root(Request $request, $first)
    {
        // Handle special cases
        if ($first === 'auth-login') {
            return redirect()->route('login');
        }
        
        // Convert hyphens to dots for view paths
        $viewName = str_replace('-', '.', $first);
        
        try {
            return view($viewName);
        } catch (\Exception $e) {
            // If view doesn't exist, try the original name
            return view($first);
        }
    }

    /**
     * second level route
     */
    public function secondLevel(Request $request, $first, $second)
    {
        // Special handling for payment settings page
        if ($first === 'payment' && $second === 'settings') {
            $providers = ServiceProvider::with(['paymentSettings'])
                ->where('verification_status', 'approved')
                ->where('is_active', true)
                ->select('id', 'user_id', 'business_name', 'commission_rate')
                ->get()
                ->map(function($provider) {
                    $settings = $provider->paymentSettings;
                    
                    return [
                        'id' => $provider->id,
                        'name' => $provider->business_name,
                        'tax' => $settings ? $settings->tax_rate : 15, // Use saved or default
                        'commission' => $settings ? $settings->commission_rate : ($provider->commission_rate ?? 10),
                        'currency' => $settings ? $settings->currency : 'SAR'
                    ];
                });
            
            // Load MyFatoorah settings from database
            $myfatoorahSettings = \DB::table('settings')
                ->where('group', 'payment')
                ->whereIn('key', [
                    'myfatoorah_enabled',
                    'myfatoorah_country',
                    'myfatoorah_currency',
                    'myfatoorah_merchant_id',
                    'myfatoorah_webhook_url',
                    'myfatoorah_payment_methods',
                    'myfatoorah_min_amount',
                    'myfatoorah_max_amount',
                    'myfatoorah_invoice_expiry',
                    'myfatoorah_language',
                    'myfatoorah_descriptor',
                    'myfatoorah_notes'
                ])
                ->pluck('value', 'key')
                ->toArray();
            
            return view($first . '.' . $second, compact('providers', 'myfatoorahSettings'));
        }
        
        return view($first . '.' . $second);
    }

    /**
     * third level route
     */
    public function thirdLevel(Request $request, $first, $second, $third)
    {
        return view($first . '.' . $second . '.' . $third);
    }
}
