<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome to DeliverIt Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <style>
    /* Some clients strip <style>, so we’ll also inline critical styles below */
    body {
      font-family: Arial, sans-serif;
      background: #f8f9fa;
      padding: 40px;
      margin: 0;
    }
    .email-container {
      max-width: 600px;
      margin: auto;
      background: #ffffff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
      text-align: center;
    }
    .logo {
      width: 120px;
      margin-bottom: 20px;
    }
    .hero {
      width: 100%;
      border-radius: 8px;
      margin: 10px 0 20px 0;
      display: block;
    }
    .headline {
      margin: 0 0 10px 0;
      color: #1e293b;
      font-size: 22px;
      line-height: 1.3;
    }
    .subtext {
      color: #4b5563;
      font-size: 14px;
      line-height: 1.6;
      margin-bottom: 24px;
    }
    .btn {
      display: inline-block;
      padding: 12px 22px;
      border-radius: 6px;
      text-decoration: none;
      background: #1e88e5;
      color: #ffffff !important;
      font-weight: bold;
      font-size: 14px;
    }
    .features {
      text-align: left;
      margin: 24px 0;
      padding: 0 6px;
    }
    .features h3 {
      color: #1f2937;
      font-size: 16px;
      margin-bottom: 8px;
    }
    .features ul {
      padding-left: 18px;
      margin: 0;
      color: #4b5563;
      line-height: 1.6;
      font-size: 14px;
    }
    .footer {
      font-size: 12px;
      color: #888;
      margin-top: 30px;
      line-height: 1.6;
    }
    .divider {
      height: 1px;
      background: #eee;
      margin: 24px 0;
      border: 0;
    }
    @media (max-width: 640px) {
      body { padding: 16px; }
      .email-container { padding: 22px; }
    }
  </style>
</head>
<body style="font-family: Arial, sans-serif; background:#f8f9fa; padding:40px; margin:0;">
  <div class="email-container" style="max-width:600px; margin:auto; background:#ffffff; padding:30px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.05); text-align:center;">
    <img src="https://deliveritpharmacy.com/wp-content/uploads/2024/04/Logo-Resurrgaction-B-Vertical.png"
         alt="DeliverIt Logo"
         class="logo"
         style="width:120px; margin-bottom:20px;">

    @if(!empty($heroUrl))
      <img src="{{ $heroUrl }}" alt="" class="hero" style="width:100%; border-radius:8px; margin:10px 0 20px 0; display:block;">
    @endif

    <h2 class="headline" style="margin:0 0 10px 0; color:#1e293b; font-size:22px; line-height:1.3;">
      Welcome, {{ $name }}!
    </h2>

    <p class="subtext" style="color:#4b5563; font-size:14px; line-height:1.6; margin-bottom:24px;">
      @if(!empty($isAdmin) && $isAdmin)
        Your account has <strong>admin privileges</strong>. You can manage users, settings, and more.
      @else
        Thanks for joining <strong>DeliverIt Portal</strong> — we’re excited to have you on board.
        Your account has been created successfully.
      @endif
    </p>

    <p style="margin: 0 0 20px 0;">
      <a href="{{ $ctaUrl ?? 'https://portal.deliveritgroup.us/' }}" class="btn"
         style="display:inline-block; padding:12px 22px; border-radius:6px; text-decoration:none; background:#1e88e5; color:#ffffff !important; font-weight:bold; font-size:14px;">
        Go to your Portal
      </a>
    </p>

    <hr class="divider" style="height:1px; background:#eee; margin:24px 0; border:0;">

    <div class="features" style="text-align:left; margin:24px 0; padding:0 6px;">
      <h3 style="color:#1f2937; font-size:16px; margin-bottom:8px;">What you can do:</h3>

      @if(!empty($features) && is_array($features))
        <ul style="padding-left:18px; margin:0; color:#4b5563; line-height:1.6; font-size:14px;">
          @foreach($features as $item)
            <li>{{ $item }}</li>
          @endforeach
        </ul>
      @else
        <ul style="padding-left:18px; margin:0; color:#4b5563; line-height:1.6; font-size:14px;">
          <li>Track prescription status in real time.</li>
          <li>Access patient updates and reports.</li>
          <li>Manage your clinic’s information securely.</li>
        </ul>
      @endif
    </div>

    <p class="subtext" style="color:#4b5563; font-size:14px; line-height:1.6; margin-bottom:0;">
      Need help? Contact us at
      <a href="mailto:ITdeliveritgroup@deliveritpharmacy.com" style="color:#1e88e5; text-decoration:none;">ITdeliveritgroup@deliveritpharmacy.com</a>.
    </p>

    <div class="footer" style="font-size:12px; color:#888; margin-top:30px; line-height:1.6;">
      If you didn’t create this account, you can safely ignore this email.<br>
      © {{ date('Y') }} DeliverIt. All rights reserved.
    </div>
  </div>
</body>
</html>
