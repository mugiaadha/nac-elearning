@extends('emails.layout')

@section('content')
<div style="background:#e0e7ff;padding:18px 0;border-radius:8px;text-align:center;margin-bottom:20px;">
    <span style="font-size:1.1rem;color:#1e40af;">Kode OTP Anda:</span><br>
    <span
        style="display:inline-block;font-size:2.2rem;font-weight:700;letter-spacing:0.2em;color:#1e40af;background:#fff;padding:8px 24px;border-radius:8px;margin-top:8px;">{{ $otp }}</span>
</div>
<p style="font-size:1rem;margin-bottom:12px;text-align:center;">Masukkan kode di atas untuk melanjutkan proses
    verifikasi akun Anda di <b>NAC Tax Center</b>.</p>
<p style="font-size:0.97rem;color:#64748b;text-align:center;margin-bottom:0;">Jangan berikan kode ini ke
    siapapun.<br>Berlaku selama <b>10 menit</b>.</p>
@endsection