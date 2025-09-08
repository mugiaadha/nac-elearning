<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>{{ $subject ?? 'NAC Tax Center' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body
    style="margin:0;padding:0;background:linear-gradient(135deg,#e0e7ff 0%,#f8fafc 100%);font-family:'Inter',Arial,sans-serif;min-height:100vh;">
    <div style="max-width:480px;margin:100px auto 24px auto;padding:0 12px;">
        <div
            style="background:#fff;border-radius:18px;box-shadow:0 6px 32px rgba(30,64,175,0.10),0 1.5px 6px rgba(30,64,175,0.07);padding:36px 28px 28px 28px;">
            <div style="text-align:center;margin-bottom:28px;">
                <img src="https://backend.nacademy.my.id/storage/logo/1837792627993646.png" alt="NAC Tax Center"
                    style="height:54px;margin-bottom:10px;">
                <h2 style="margin:0;font-size:1.7rem;font-weight:700;color:#1e40af;letter-spacing:0.01em;">
                    {{ $title ?? 'NAC Tax Center' }}</h2>
            </div>
            @yield('content')
            <hr style="margin:32px 0 16px 0;border:none;border-top:1.5px solid #e0e7ff;">
            <div style="text-align:center;font-size:0.97rem;color:#64748b;">NAC Tax Center &copy; {{ date('Y') }}</div>
        </div>
        <div style="text-align:center;margin-top:18px;color:#a5b4fc;font-size:0.93rem;">Solusi Perpajakan Modern untuk
            Indonesia</div>
    </div>
</body>

</html>