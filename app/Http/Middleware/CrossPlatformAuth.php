<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

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
            $sessionData = cache($sessionKey);
            
            if ($sessionData && isset($sessionData['user_id'])) {
                $user = User::find($sessionData['user_id']);
                
                if ($user) {
                    auth()->setUser($user);
                    auth('web')->setUser($user);
                    
                    // Set session for backend compatibility
                    session(['user_id' => $user->id]);
                    session(['user_email' => $user->email]);
                    session(['user_name' => $user->name]);
                    
                    return $next($request);
                }
            }
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized'
        ], 401);
    }
}
