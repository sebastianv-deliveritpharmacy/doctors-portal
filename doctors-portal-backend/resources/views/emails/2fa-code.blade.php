<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your 2FA Verification Code</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f8f9fa;
      padding: 40px;
    }

    .email-container {
      max-width: 600px;
      margin: auto;
      background: white;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
      text-align: center;
    }

    .logo {
      width: 120px;
      margin-bottom: 20px;
    }

    .code-box {
      font-size: 32px;
      font-weight: bold;
      color: #1e88e5;
      margin: 20px 0;
      letter-spacing: 6px;
    }

    .footer {
      font-size: 12px;
      color: #888;
      margin-top: 30px;
    }
  </style>
</head>
<body>
  <div class="email-container">
    <img src="https://deliveritpharmacy.com/wp-content/uploads/2024/04/Logo-Resurrgaction-B-Vertical.png" alt="DeliverIt Logo" class="logo">

    <h2>Your verification code</h2>
    <p>Use the code below to complete your login:</p>

    <div class="code-box">{{ $code }}</div>

    <p>This code will expire in 10 minutes.</p>

    <div class="footer">
      If you didn’t request this, you can safely ignore this email.
    </div>
  </div>
</body>
</html>
