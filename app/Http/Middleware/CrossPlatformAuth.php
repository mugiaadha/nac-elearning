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
        // Check Laravel default auth FIRST (untuk user yang login normal)
        if (auth()->check()) {
            // User sudah login via Laravel auth biasa
            $user = auth()->user();
            Log::info('CrossPlatformAuth: User already authenticated via Laravel auth', ['user_id' => $user->id]);
            return $next($request);
        }
        
        // Check for API token (for frontend)
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
                    
                    // Jika akses via session_key parameter di URL, set flag untuk auto-redirect
                    if ($request->has('session_key')) {
                        Log::info('CrossPlatformAuth: Session key authentication successful, setting redirect flag', ['from' => $request->fullUrl()]);
                        
                        // Set attribute untuk route bisa detect bahwa ini dari session_key
                        $request->attributes->set('authenticated_via_session_key', true);
                        $request->attributes->set('should_redirect_dashboard', true);
                    }
                    
                    return $next($request);
                }
            } else {
                Log::warning('CrossPlatformAuth: Session data not found or invalid', ['session_key' => $sessionKey]);
            }
        }
        
        // Jika semua authentication method gagal
        // Check apakah request ini dari browser (expect HTML) atau API (expect JSON)
        if ($request->expectsJson() || $request->is('api/*')) {
            // Request dari API atau AJAX - return JSON response
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        } else {
            // Request dari browser biasa - redirect ke login
            return redirect()->route('login')->with('error', 'Please login to access this page');
        }
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
