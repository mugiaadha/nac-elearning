<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VerifyRecaptcha
{
    public function handle(Request $request, Closure $next)
    {
        // pastikan token ada
        if (!$request->has('captcha')) {
            return response()->json(['message' => 'Captcha wajib diisi.'], 422);
        }

        $captchaToken = $request->input('captcha');

        // kirim ke Google
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('RECAPTCHA_SECRET_KEY'),
            'response' => $captchaToken,
            'remoteip' => $request->ip(),
        ]);

        $captchaResult = $response->json();

        if (!($captchaResult['success'] ?? false)) {
            return response()->json(['message' => 'Captcha tidak valid.'], 422);
        }

        // lanjut request berikutnya
        return $next($request);
    }
}
