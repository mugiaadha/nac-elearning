<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::prefix('backend')->group(function () {
    Route::post('/login', function (Request $request) {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return response()->json(['message' => 'Login success']);
        }
        return response()->json(['message' => 'Invalid credentials'], 401);
    });
});
