<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;
use Exception;

class AuthController extends BaseController
{
    /**
     * Login user and create session
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
                'remember_me' => 'boolean'
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors(), 422);
            }

            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                
                // Generate simple token for API usage
                $tokenExpiry = $request->remember_me ? now()->addDays(30) : now()->addHours(24);
                $token = 'token_' . $user->id . '_' . md5($user->email . time());

                // Set session for backend compatibility
                session(['user_id' => $user->id]);
                session(['user_email' => $user->email]);
                session(['user_name' => $user->name]);
                session(['api_token' => $token]);
                
                // Create shared session data
                $sessionData = [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                    'role' => $user->role ?? 'user',
                    'token' => $token,
                    'logged_in_at' => now()->toDateTimeString(),
                    'expires_at' => $tokenExpiry->toDateTimeString()
                ];

                // Store in cache for cross-platform access
                $cacheKey = "user_session_{$user->id}";
                cache([$cacheKey => $sessionData], $tokenExpiry);
                
                // Also store token mapping
                cache(["api_token_{$token}" => $user->id], $tokenExpiry);

                return $this->sendResponse([
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_at' => $tokenExpiry->toDateTimeString(),
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role ?? 'user'
                    ],
                    'session_key' => $cacheKey // For cross-platform session access
                ], 'Login berhasil');

            } else {
                return $this->sendError('Email atau password salah', [], 401);
            }

        } catch (Exception $e) {
            return $this->handleException($e, 'User login');
        }
    }

    /**
     * Logout user
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        try {
            // Get user from token or session
            $user = $this->getUserFromRequest($request);
            
            if ($user) {
                // Clear cache session
                $cacheKey = "user_session_{$user->id}";
                cache()->forget($cacheKey);
                
                // Clear token mapping
                $token = $request->bearerToken() ?? session('api_token');
                if ($token) {
                    cache()->forget("api_token_{$token}");
                }
                
                // Clear Laravel session
                session()->flush();
            }

            return $this->sendResponse([], 'Logout berhasil');

        } catch (Exception $e) {
            return $this->handleException($e, 'User logout');
        }
    }

    /**
     * Get user from request (token or session)
     *
     * @param Request $request
     * @return User|null
     */
    private function getUserFromRequest(Request $request)
    {
        // Try from bearer token
        $token = $request->bearerToken();
        if ($token) {
            $userId = cache("api_token_{$token}");
            if ($userId) {
                return User::find($userId);
            }
        }

        // Try from session
        if (session('user_id')) {
            return User::find(session('user_id'));
        }

        // Try from authenticated user
        return $request->user();
    }

    /**
     * Get authenticated user
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function me(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            
            if (!$user) {
                return $this->sendError('User tidak ditemukan', [], 401);
            }

            // Update cache session
            $cacheKey = "user_session_{$user->id}";
            $sessionData = [
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'role' => $user->role ?? 'user',
                'last_activity' => now()->toDateTimeString()
            ];
            cache([$cacheKey => $sessionData], now()->addHours(24));

            return $this->sendResponse([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role ?? 'user',
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'session_key' => $cacheKey
            ], 'User data berhasil diambil');

        } catch (Exception $e) {
            return $this->handleException($e, 'Getting user data');
        }
    }

    /**
     * Check session from cache (for cross-platform)
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function checkSession(Request $request)
    {
        try {
            $sessionKey = $request->input('session_key');
            $userId = $request->input('user_id');

            if (!$sessionKey && !$userId) {
                return $this->sendError('Session key atau user ID diperlukan', [], 400);
            }

            // Generate cache key if not provided
            if (!$sessionKey && $userId) {
                $sessionKey = "user_session_{$userId}";
            }

            $sessionData = cache($sessionKey);

            if (!$sessionData) {
                return $this->sendError('Session tidak ditemukan atau sudah expired', [], 401);
            }

            return $this->sendResponse($sessionData, 'Session masih aktif');

        } catch (Exception $e) {
            return $this->handleException($e, 'Checking session');
        }
    }

    /**
     * Refresh token
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function refresh(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            
            if (!$user) {
                return $this->sendError('User tidak ditemukan', [], 401);
            }

            // Revoke old token
            $oldToken = $request->bearerToken();
            if ($oldToken) {
                cache()->forget("api_token_{$oldToken}");
            }

            // Create new token
            $tokenExpiry = now()->addHours(24);
            $newToken = 'token_' . $user->id . '_' . md5($user->email . time());

            // Update cache session
            $cacheKey = "user_session_{$user->id}";
            $sessionData = [
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'role' => $user->role ?? 'user',
                'token' => $newToken,
                'refreshed_at' => now()->toDateTimeString(),
                'expires_at' => $tokenExpiry->toDateTimeString()
            ];
            cache([$cacheKey => $sessionData], $tokenExpiry);
            
            // Store new token mapping
            cache(["api_token_{$newToken}" => $user->id], $tokenExpiry);

            return $this->sendResponse([
                'token' => $newToken,
                'token_type' => 'Bearer',
                'expires_at' => $tokenExpiry->toDateTimeString(),
                'session_key' => $cacheKey
            ], 'Token berhasil direfresh');

        } catch (Exception $e) {
            return $this->handleException($e, 'Refreshing token');
        }
    }

    /**
     * Validate token (for backend integration)
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function validateToken(Request $request)
    {
        try {
            $token = $request->input('token') ?? $request->bearerToken();
            
            if (!$token) {
                return $this->sendError('Token diperlukan', [], 400);
            }

            // Check if token exists in cache
            $userId = cache("api_token_{$token}");
            
            if (!$userId) {
                return $this->sendError('Token tidak valid atau sudah expired', [], 401);
            }

            $user = User::find($userId);
            
            if (!$user) {
                // Clean up invalid token
                cache()->forget("api_token_{$token}");
                return $this->sendError('User tidak ditemukan', [], 401);
            }

            // Check session data for expiry
            $sessionKey = "user_session_{$userId}";
            $sessionData = cache($sessionKey);
            
            if (!$sessionData) {
                // Session expired, clean up token
                cache()->forget("api_token_{$token}");
                return $this->sendError('Session sudah expired', [], 401);
            }

            return $this->sendResponse([
                'valid' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role ?? 'user'
                ],
                'expires_at' => $sessionData['expires_at'] ?? null
            ], 'Token valid');

        } catch (Exception $e) {
            return $this->handleException($e, 'Validating token');
        }
    }
}
