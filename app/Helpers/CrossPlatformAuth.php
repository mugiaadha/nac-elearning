<?php

namespace App\Helpers;

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class CrossPlatformAuth
{
    /**
     * Get user from token or session
     *
     * @param string|null $token
     * @param string|null $sessionKey
     * @return User|null
     */
    public static function getUser($token = null, $sessionKey = null)
    {
        // Try token first
        if ($token) {
            $accessToken = PersonalAccessToken::findToken($token);
            if ($accessToken && $accessToken->expires_at > now()) {
                return $accessToken->tokenable;
            }
        }

        // Try session key
        if ($sessionKey) {
            $sessionData = cache($sessionKey);
            if ($sessionData && isset($sessionData['user_id'])) {
                return User::find($sessionData['user_id']);
            }
        }

        // Try Laravel session
        if (session('user_id')) {
            return User::find(session('user_id'));
        }

        return null;
    }

    /**
     * Check if user is authenticated
     *
     * @param string|null $token
     * @param string|null $sessionKey
     * @return bool
     */
    public static function check($token = null, $sessionKey = null)
    {
        return self::getUser($token, $sessionKey) !== null;
    }

    /**
     * Login user and create cross-platform session
     *
     * @param User $user
     * @param bool $remember
     * @return array
     */
    public static function loginUser(User $user, $remember = false)
    {
        // Create token
        $tokenExpiry = $remember ? now()->addDays(30) : now()->addHours(24);
        $token = $user->createToken('auth_token', ['*'], $tokenExpiry)->plainTextToken;

        // Set Laravel session
        session(['user_id' => $user->id]);
        session(['user_email' => $user->email]);
        session(['user_name' => $user->name]);

        // Create cache session
        $sessionData = [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'role' => $user->role ?? 'user',
            'logged_in_at' => now()->toDateTimeString(),
            'expires_at' => $tokenExpiry->toDateTimeString()
        ];

        $cacheKey = "user_session_{$user->id}";
        cache([$cacheKey => $sessionData], $tokenExpiry);

        return [
            'token' => $token,
            'session_key' => $cacheKey,
            'expires_at' => $tokenExpiry,
            'user' => $user
        ];
    }

    /**
     * Logout user from all platforms
     *
     * @param User $user
     * @return void
     */
    public static function logoutUser(User $user)
    {
        // Clear tokens
        $user->tokens()->delete();

        // Clear cache session
        $cacheKey = "user_session_{$user->id}";
        cache()->forget($cacheKey);

        // Clear Laravel session
        session()->flush();
    }

    /**
     * Get user ID from request (multiple sources)
     *
     * @param \Illuminate\Http\Request $request
     * @return int|null
     */
    public static function getUserId($request)
    {
        // From authenticated user
        if ($request->user()) {
            return $request->user()->id;
        }

        // From token
        $token = $request->bearerToken() ?? $request->input('token');
        if ($token) {
            $accessToken = PersonalAccessToken::findToken($token);
            if ($accessToken && $accessToken->expires_at > now()) {
                return $accessToken->tokenable->id;
            }
        }

        // From session key
        $sessionKey = $request->input('session_key') ?? $request->header('X-Session-Key');
        if ($sessionKey) {
            $sessionData = cache($sessionKey);
            if ($sessionData && isset($sessionData['user_id'])) {
                return $sessionData['user_id'];
            }
        }

        // From Laravel session
        return session('user_id');
    }

    /**
     * Generate session key for user
     *
     * @param int $userId
     * @return string
     */
    public static function generateSessionKey($userId)
    {
        return "user_session_{$userId}";
    }

    /**
     * Validate session from any platform
     *
     * @param string $sessionKey
     * @return array|null
     */
    public static function validateSession($sessionKey)
    {
        $sessionData = cache($sessionKey);
        
        if (!$sessionData || !isset($sessionData['user_id'])) {
            return null;
        }

        // Check if user still exists
        $user = User::find($sessionData['user_id']);
        if (!$user) {
            cache()->forget($sessionKey);
            return null;
        }

        return $sessionData;
    }
}
