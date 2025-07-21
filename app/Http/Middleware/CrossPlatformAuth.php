<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class CrossPlatformAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check for API token first (for frontend)
        $token = $request->bearerToken() ?? $request->input('token');
        
        if ($token) {
            $userId = cache("api_token_{$token}");
            
            if ($userId) {
                $user = User::find($userId);
                
                if ($user) {
                    // Set user for both API and web
                    auth()->setUser($user);
                    auth('web')->setUser($user);
                    
                    // Set session for backend compatibility
                    session(['user_id' => $user->id]);
                    session(['user_email' => $user->email]);
                    session(['user_name' => $user->name]);
                    session(['api_token' => $token]);
                    
                    return $next($request);
                }
            }
        }
        
        // Check for session (for backend)
        if (session('user_id')) {
            $user = User::find(session('user_id'));
            
            if ($user) {
                auth()->setUser($user);
                auth('web')->setUser($user);
                return $next($request);
            }
        }
        
        // Check cache session (cross-platform)
        $sessionKey = $request->input('session_key') ?? $request->header('X-Session-Key');
        if ($sessionKey) {
            Log::info('CrossPlatformAuth: Checking session key', ['session_key' => $sessionKey]);
            
            $sessionData = cache($sessionKey);
            Log::info('CrossPlatformAuth: Session data from cache', ['data' => $sessionData]);
            
            if ($sessionData && isset($sessionData['user_id'])) {
                $user = User::find($sessionData['user_id']);
                Log::info('CrossPlatformAuth: User found', ['user_id' => $sessionData['user_id'], 'user' => $user ? $user->toArray() : null]);
                
                if ($user) {
                    auth()->setUser($user);
                    auth('web')->setUser($user);
                    
                    // Set session for backend compatibility
                    session(['user_id' => $user->id]);
                    session(['user_email' => $user->email]);
                    session(['user_name' => $user->name]);
                    
                    Log::info('CrossPlatformAuth: User authenticated successfully', ['user_id' => $user->id]);
                    
                    // Jika akses via session_key parameter di URL, redirect ke dashboard bersih
                    if ($request->has('session_key')) {
                        Log::info('CrossPlatformAuth: Auto-redirecting to dashboard', ['from' => $request->fullUrl()]);
                        
                        // Tentukan dashboard berdasarkan role user
                        $dashboardUrl = $this->getDashboardUrl($user);
                        
                        return redirect()->to($dashboardUrl);
                    }
                    
                    return $next($request);
                }
            } else {
                Log::warning('CrossPlatformAuth: Session data not found or invalid', ['session_key' => $sessionKey]);
            }
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized'
        ], 401);
    }
    
    /**
     * Get appropriate dashboard URL based on user role
     */
    private function getDashboardUrl($user)
    {
        // Cek role user untuk tentukan dashboard yang tepat
        $role = $user->role ?? 'user';
        
        switch (strtolower($role)) {
            case 'admin':
                return '/admin/dashboard';
            case 'instructor':
                return '/instructor/dashboard';
            case 'user':
            default:
                // User biasa redirect ke /dashboard
                return '/dashboard';
        }
    }
}
