<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Priority 1: Check if locale is in request query parameter
        if ($request->has('lang')) {
            $locale = $request->get('lang');
            Session::put('locale', $locale);
            // Log::info('SetLocale: Using query param lang=' . $locale);
        }
        // Priority 2: Check session
        elseif (Session::has('locale')) {
            $locale = Session::get('locale');
            // Log::info('SetLocale: Using session locale=' . $locale);
        }
        // Priority 3: Check Accept-Language header
        elseif ($request->hasHeader('Accept-Language')) {
            $langHeader = $request->header('Accept-Language');
            $primary = substr(strtolower(trim(explode(',', $langHeader)[0])), 0, 2);
            $locale = $primary;
            // Log::info('SetLocale: Using Accept-Language header=' . $locale);
        }
        // Default to app locale
        else {
            $locale = config('app.locale');
            // Log::info('SetLocale: Using default locale=' . $locale);
        }

        // Validate locale
        $supportedLocales = ['en', 'ar'];
        if (!in_array($locale, $supportedLocales)) {
            $locale = config('app.fallback_locale');
        }

        // Set application locale
        App::setLocale($locale);
        
        // Log::info('SetLocale: Final locale set to ' . App::getLocale() . ' (validated from: ' . $locale . ')');
        // Log::info('SetLocale: Request URL: ' . $request->fullUrl());

        // Set direction for RTL languages
        $rtlLanguages = ['ar'];
        $direction = in_array($locale, $rtlLanguages) ? 'rtl' : 'ltr';
        $isRtl = $direction === 'rtl';
        
        // Log for debugging
        // Log::info('SetLocale Middleware - Locale: ' . $locale . ', Direction: ' . $direction . ', isRtl: ' . ($isRtl ? 'true' : 'false'));
        
        // Store in config for easy access
        config(['app.direction' => $direction]);
        config(['app.is_rtl' => $isRtl]);
        
        // Share with views using View facade
        \Illuminate\Support\Facades\View::share('currentLocale', $locale);
        \Illuminate\Support\Facades\View::share('direction', $direction);
        \Illuminate\Support\Facades\View::share('isRtl', $isRtl);

        return $next($request);
    }
}
