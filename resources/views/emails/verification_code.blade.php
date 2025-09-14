<!doctype html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>{{ $appName }} Verification Code</title>
  <style>
    /* Basic email reset */
    body { background: #f5f7fb; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #111827; }
    .container { width: 100%; padding: 24px 0; }
    .card { max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 12px; box-shadow: 0 6px 18px rgba(0,0,0,0.06); overflow: hidden; }
    .header { background: linear-gradient(135deg, #4f46e5, #7c3aed); color: #fff; padding: 24px; text-align: center; }
    .header h1 { margin: 0; font-size: 20px; font-weight: 700; }
    .content { padding: 24px; line-height: 1.6; }
    .code { font-size: 28px; letter-spacing: 6px; font-weight: 800; text-align: center; background: #f3f4f6; border-radius: 10px; padding: 16px; margin: 16px 0 8px; }
    .muted { color: #6b7280; font-size: 13px; }
    .cta { display: inline-block; padding: 12px 18px; background: #4f46e5; color: #fff !important; text-decoration: none; border-radius: 8px; font-weight: 600; }
    .footer { text-align: center; color: #9ca3af; font-size: 12px; padding: 16px 24px 24px; }
  </style>
</head>
<body>
  <div class="container">
    <div class="card">
      <div class="header">
        <h1>{{ $appName }} Security Code</h1>
      </div>
      <div class="content">
        <p>Hello,</p>
        <p>Use the 6-digit code below to continue signing in to <strong>{{ $appName }}</strong>.</p>
        <div class="code">{{ $code }}</div>
        <p class="muted">This code will expire soon. If you didn’t request this, you can safely ignore this email.</p>
      </div>
      <div class="footer">
        <p>© {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
      </div>
    </div>
  </div>
</body>
</html>
