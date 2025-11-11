<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // If already authenticated, redirect to dashboard
        if (Auth::check()) {
            return redirect('/dashboards/index');
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Find user first to check status before authentication
        $user = User::where('email', $request->email)->first();

        // Check if user exists
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        // Check if user is a dashboard user (has admin roles)
        $dashboardRoles = ['super_admin', 'admin', 'manager', 'support_agent', 'content_manager', 'analyst'];
        if (!$user->hasAnyRole($dashboardRoles)) {
            return back()->withErrors([
                'email' => 'You do not have permission to access the admin panel.',
            ])->onlyInput('email');
        }

        // Check if user account is active BEFORE login
        if ($user->status !== 'active' || !$user->is_active) {
            return back()->withErrors([
                'email' => 'Your account has been ' . $user->status . '. Please contact administrator.',
            ])->onlyInput('email');
        }

        // All checks passed, now authenticate
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // Update last login timestamp
        $user->update(['last_login_at' => now()]);

        // Create API token for dashboard API calls
        $tokenExpiry = $request->boolean('remember') ? now()->addDays(7) : now()->addHours(24);
        $token = $user->createToken('dashboard-token', ['*'], $tokenExpiry)->plainTextToken;
        
        // Store token in session for JavaScript access
        $request->session()->put('api_token', $token);

        return redirect()->intended('/dashboards/index');
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        // Revoke current access token if exists
        if ($request->user()) {
            $request->user()->currentAccessToken()?->delete();
            // Revoke ALL tokens for this user
            $request->user()->tokens()->delete();
        }

        Auth::guard('web')->logout();

        // Clear all session data
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->flush();

        // Clear cookies
        $response = redirect('/login');
        
        // Add cache control headers to prevent back button access
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');

        return $response;
    }
}
