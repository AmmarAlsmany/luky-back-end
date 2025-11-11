<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Setting;
use App\Models\User;

class SettingsController extends Controller
{
    /**
     * Display the settings page
     */
    public function index()
    {
        // Load settings from database
        $settings = [
            'contact_email' => Setting::get('contact_email', 'support@luky.app'),
            'contact_phone' => Setting::get('contact_phone', '+966 501234567'),
            'contact_address' => Setting::get('contact_address', 'Riyadh, Saudi Arabia'),
        ];
        
        return view('settings.settings', compact('settings'));
    }

    /**
     * Update general settings
     */
    public function updateSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:20',
            'contact_address' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Save settings to database
            if ($request->has('contact_email')) {
                Setting::set('contact_email', $request->contact_email);
            }
            
            if ($request->has('contact_phone')) {
                Setting::set('contact_phone', $request->contact_phone);
            }
            
            if ($request->has('contact_address')) {
                Setting::set('contact_address', $request->contact_address);
            }

            // Return updated settings
            $settings = [
                'contact_email' => Setting::get('contact_email', 'support@luky.app'),
                'contact_phone' => Setting::get('contact_phone', '+966 501234567'),
                'contact_address' => Setting::get('contact_address', 'Riyadh, Saudi Arabia'),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully',
                'settings' => $settings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update admin password
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 422);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully'
        ]);
    }
}
