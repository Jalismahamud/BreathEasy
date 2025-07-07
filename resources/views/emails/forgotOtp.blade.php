@extends('emails.masterEmailLayout')

@section('content')
    <p style="font-size:16px; margin-bottom: 18px; text-align:left;">Hello {{ $user->f_name . ' ' . $user->l_name ?? 'User' }},</p>
    <h1>Forgot Password OTP</h1>
    <p>Please use the code below in the app to reset your password.</p>
    <div class="verification-box">
        <div class="code-label">Your OTP Code</div>
        <div class="otp">{{ $otp }}</div>
        <div class="note">This code will expire in 5 minutes.</div>
    </div>
@endsection
