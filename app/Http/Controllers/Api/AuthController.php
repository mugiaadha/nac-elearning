<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Exception;

class AuthController extends BaseController
{
    /**
     * Register a new user
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Data tidak valid', $validator->errors(), 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken('api-token')->plainTextToken;

            return $this->sendResponse([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Registrasi berhasil', 201);
        } catch (Exception $e) {
            return $this->handleException($e, 'Register process');
        }
    }
    
    /**
     * Login with token (Sanctum)
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:6',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Data tidak valid', $validator->errors(), 422);
            }

            // Find user
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return $this->sendError('Email atau password salah', [], 401);
            }

            // Create token
            $token = $user->createToken('api-token')->plainTextToken;

            return $this->sendResponse([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Login berhasil');

        } catch (Exception $e) {
            return $this->handleException($e, 'Login process');
        }
    }

    /**
     * Login with session
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function sessionLogin(Request $request)
    {
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:6',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Data tidak valid', $validator->errors(), 422);
            }

            $credentials = $request->only('email', 'password');
            
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                return $this->sendResponse([
                    'user' => $user
                ], 'Login berhasil');
            }

            return $this->sendError('Email atau password salah', [], 401);

        } catch (Exception $e) {
            return $this->handleException($e, 'Session login process');
        }
    }

    /**
     * Get authenticated user data
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function user(Request $request)
    {
        try {
            return $this->sendResponse(
                $request->user(),
                'Data user berhasil diambil'
            );
        } catch (Exception $e) {
            return $this->handleException($e, 'Getting user data');
        }
    }

    /**
     * Logout (revoke token)
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            
            return $this->sendResponse(
                [],
                'Logout berhasil'
            );
        } catch (Exception $e) {
            return $this->handleException($e, 'Logout process');
        }
    }

    /**
     * Session logout
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function sessionLogout(Request $request)
    {
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return $this->sendResponse(
                [],
                'Logout berhasil'
            );
        } catch (Exception $e) {
            return $this->handleException($e, 'Session logout process');
        }
    }
}
