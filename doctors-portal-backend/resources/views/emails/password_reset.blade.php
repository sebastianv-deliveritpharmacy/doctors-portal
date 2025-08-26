<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Reset your password</title>
  <style>
    body { margin:0; padding:0; background:#f8f9fa; font-family: Arial, sans-serif; color:#222; }
    .wrap { width:100%; padding:40px 12px; box-sizing:border-box; }
    .email-container {
      max-width:600px; margin:0 auto; background:#fff; padding:30px; border-radius:8px;
      box-shadow:0 4px 10px rgba(0,0,0,0.05); text-align:center;
    }
    .logo { width:120px; height:auto; margin-bottom:20px; }
    h2 { margin:0 0 12px; font-size:22px; color:#111; }
    p { margin:10px 0; line-height:1.55; }
    .btn {
      display:inline-block; padding:12px 20px; border-radius:6px; text-decoration:none;
      background:#1e88e5; color:#fff; font-weight:700; margin:16px 0;
    }
    .hr { height:1px; background:#eee; border:none; margin:24px 0; }
    .footer { font-size:12px; color:#888; margin-top:20px; }
    .link-fallback { word-break:break-all; color:#1e88e5; text-decoration:underline; font-size:13px; }
    @media (max-width:620px){ .email-container{ padding:22px; } .logo{ width:110px; } }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="email-container">
      <img
        src="https://deliveritpharmacy.com/wp-content/uploads/2024/04/Logo-Resurrgaction-B-Vertical.png"
        alt="DeliverIt Logo"
        class="logo"
      />

      <h2>Reset your password</h2>
      <p>Hi {{ $name ?? 'there' }},</p>
      <p>We received a request to reset the password for your DeliverIt Health account.</p>

      <p>
        <a href="{{ $resetUrl }}" class="btn">Reset your password</a>
      </p>

      <p>If the button doesn’t work, copy and paste this link into your browser:</p>
      <p><a class="link-fallback" href="{{ $resetUrl }}">{{ $resetUrl }}</a></p>

      <hr class="hr" />

      <p class="footer">
        For security, the link will expire after a short period.
        If you didn’t request this, you can safely ignore this email.
      </p>
    </div>
  </div>
</body>
</html>
