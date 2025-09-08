@extends('emails.layout')

@section('content')
    <h3 style="color:#1e40af;margin-bottom:12px;">Pesan Baru dari Pengunjung NAC Tax Center</h3>
    <table style="width:100%;margin-bottom:18px;">
        <tr>
            <td style="color:#64748b;width:90px;">Nama</td>
            <td><b>{{ $name }}</b></td>
        </tr>
        <tr>
            <td style="color:#64748b;">Email</td>
            <td><b>{{ $email }}</b></td>
        </tr>
    </table>
    <div style="background:#e0e7ff;padding:16px;border-radius:8px;">
        <div style="color:#1e40af;font-weight:500;margin-bottom:8px;">Pesan:</div>
        <div style="color:#334155;">{{ $messageText }}</div>
    </div>
@endsection
