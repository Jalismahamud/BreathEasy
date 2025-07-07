{{-- Master Email Layout for all emails --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>{{ $title ?? config('app.name') }}</title>
  <style>
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      background: #F5F6FA;
      font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
      color: #22223B;
    }
    body {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 0;
      min-height: 100vh;
    }
    .container {
      width: 100%;
      max-width: 420px;
      background: #fff;
      border-radius: 32px;
      overflow: hidden;
      box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.10);
      border: 1.5px solid #E0E0F0;
    }
    .logo-section {
      text-align: center;
      padding: 38px 0 0 0;
    }
    .brand-name {
      font-size: 30px;
      font-weight: 700;
      color: #5F4B8B;
      letter-spacing: 1px;
      margin-bottom: 0;
      font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
    }
    .content {
      padding: 0 32px 0;
      text-align: center;
    }
    .content h1 {
      font-size: 22px;
      margin: 32px 0 10px 0;
      color: #22223B;
      font-weight: 700;
      letter-spacing: 0.5px;
    }
    .content p {
      font-size: 15px;
      color: #6C6C80;
      line-height: 1.6;
      margin-bottom: 25px;
      margin-top: 0;
    }
    .verification-box {
      background: #F5F6FA;
      border-radius: 18px;
      padding: 22px 0 18px 0;
      margin: 28px 0 18px 0;
      box-shadow: 0 2px 8px 0 rgba(95, 75, 139, 0.04);
    }
    .verification-box .code-label {
      font-size: 13px;
      color: #8A8AA0;
      margin-bottom: 8px;
    }
    .verification-box .otp, .verification-box .verification-code {
      font-size: 32px;
      font-weight: bold;
      color: #5F4B8B;
      letter-spacing: 8px;
      margin-bottom: 0;
    }
    .note, .verification-note {
      font-size: 12px;
      color: #8A8AA0;
      margin-top: 12px;
    }
    .button-container {
      margin: 22px 0;
    }
    .verify-button {
      background: linear-gradient(90deg, #5F4B8B 0%, #36B1E6 100%);
      padding: 13px 36px;
      border-radius: 32px;
      font-size: 15px;
      color: #fff;
      text-decoration: none;
      font-weight: 600;
      display: inline-block;
      border: none;
      box-shadow: 0 2px 8px 0 rgba(95, 75, 139, 0.08);
      transition: background 0.2s;
    }
    .verify-button:hover {
      background: linear-gradient(90deg, #36B1E6 0%, #5F4B8B 100%);
    }
    .footer {
      text-align: center;
      padding: 18px 0 12px 0;
      color: #8A8AA0;
      font-size: 12px;
      background: #fff;
      border-top: 1px solid #E0E0F0;
      border-radius: 0 0 32px 32px;
      margin-top: 18px;
    }
    @media screen and (max-width: 480px) {
      body {
        padding: 10px 0;
      }
      .container {
        width: 98%;
        max-width: 98vw;
        border-radius: 18px;
      }
      .otp, .verification-code {
        font-size: 22px;
      }
      .verify-button {
        padding: 10px 18px;
        font-size: 14px;
      }
      .content {
        padding: 10px 2vw 0;
      }
      .footer {
        border-radius: 0 0 18px 18px;
      }
    }
  </style>
  @stack('head')
</head>
<body>
  <div class="container">
    <div class="logo-section">
      <div class="brand-name">{{ config('app.name') }}</div>
    </div>
    <div class="content">
      @yield('content')
    </div>
    <div class="footer">
      &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </div>
  </div>
</body>
</html>
