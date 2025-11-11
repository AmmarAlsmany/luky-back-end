<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class LukyApiService
{
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        $this->baseUrl = config('services.luky_api.base_url');
        $this->token = Session::get('api_token');
    }

    /**
     * Make authenticated GET request
     */
    public function get($endpoint, $params = [])
    {
        return Http::withToken($this->token)
            ->acceptJson()
            ->timeout(30)
            ->get($this->baseUrl . $endpoint, $params);
    }

    /**
     * Make authenticated POST request
     */
    public function post($endpoint, $data = [])
    {
        return Http::withToken($this->token)
            ->acceptJson()
            ->timeout(30)
            ->post($this->baseUrl . $endpoint, $data);
    }

    /**
     * Make authenticated PUT request
     */
    public function put($endpoint, $data = [])
    {
        return Http::withToken($this->token)
            ->acceptJson()
            ->timeout(30)
            ->put($this->baseUrl . $endpoint, $data);
    }

    /**
     * Make authenticated DELETE request
     */
    public function delete($endpoint)
    {
        return Http::withToken($this->token)
            ->acceptJson()
            ->timeout(30)
            ->delete($this->baseUrl . $endpoint);
    }

    /**
     * Upload file with multipart/form-data
     */
    public function upload($endpoint, $data = [], $files = [])
    {
        $request = Http::withToken($this->token)
            ->timeout(60);

        foreach ($files as $key => $file) {
            $request->attach($key, file_get_contents($file->path()), $file->getClientOriginalName());
        }

        return $request->post($this->baseUrl . $endpoint, $data);
    }

    /**
     * Login to API and get token
     */
    public function login($email, $password, $rememberMe = false)
    {
        $response = Http::acceptJson()
            ->timeout(30)
            ->post($this->baseUrl . '/api/admin/auth/login', [
                'email' => $email,
                'password' => $password,
                'remember_me' => $rememberMe,
            ]);

        if ($response->successful()) {
            $data = $response->json();
            if ($data['success'] ?? false) {
                // Store token and user in session
                Session::put('api_token', $data['data']['token']);
                Session::put('user', $data['data']['user']);
                Session::put('remember_me', $rememberMe);

                // If remember me is checked, extend session lifetime
                if ($rememberMe) {
                    // Set session to last for 30 days (43200 minutes)
                    config(['session.lifetime' => 43200]);
                }

                return [
                    'success' => true,
                    'user' => $data['data']['user'],
                ];
            }
        }

        return [
            'success' => false,
            'message' => $response->json()['message'] ?? 'Login failed',
        ];
    }

    /**
     * Logout from API
     */
    public function logout()
    {
        if ($this->token) {
            $this->post('/api/admin/auth/logout');
        }

        Session::forget('api_token');
        Session::forget('user');

        return true;
    }

    /**
     * Check if user is authenticated
     */
    public function isAuthenticated()
    {
        return Session::has('api_token');
    }

    /**
     * Get current user
     */
    public function getCurrentUser()
    {
        return Session::get('user');
    }

    /**
     * Handle API response and extract data
     */
    public function handleResponse($response)
    {
        if ($response->successful()) {
            $data = $response->json();
            return [
                'success' => $data['success'] ?? false,
                'data' => $data['data'] ?? [],
                'message' => $data['message'] ?? '',
            ];
        }

        // Handle errors
        if ($response->status() === 401) {
            // Session expired
            $this->logout();
        }

        return [
            'success' => false,
            'data' => [],
            'message' => $response->json()['message'] ?? 'An error occurred',
            'errors' => $response->json()['errors'] ?? [],
        ];
    }

    /**
     * Get error message from response
     */
    public function getErrorMessage($response)
    {
        if ($response->successful()) {
            return null;
        }

        $data = $response->json();

        if (isset($data['message'])) {
            return $data['message'];
        }

        if (isset($data['errors'])) {
            $errors = $data['errors'];
            return is_array($errors) ? implode(', ', array_map(fn($e) => is_array($e) ? implode(', ', $e) : $e, $errors)) : $errors;
        }

        return 'An error occurred';
    }
}
