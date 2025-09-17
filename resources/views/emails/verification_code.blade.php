<!doctype html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>{{ $appName }} - Security Verification Code</title>
  <style>
    /* Brand Guidelines Compliant Email Template */
    body {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
      margin: 0;
      padding: 0;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
      color: #e2e8f0;
      min-height: 100vh;
    }
    
    .container {
      width: 100%;
      padding: 40px 20px;
      max-width: 600px;
      margin: 0 auto;
    }
    
    /* Glassmorphism Card */
    .card {
      background: rgba(30, 41, 59, 0.4);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(59, 130, 246, 0.2);
      border-radius: 16px;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
      overflow: hidden;
      margin: 0 auto;
    }
    
    /* Brand Header */
    .header {
      background: linear-gradient(135deg, #3b82f6 0%, #06b6d4 100%);
      background-image: 
        radial-gradient(circle at 20% 50%, rgba(59, 130, 246, 0.2) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(6, 182, 212, 0.2) 0%, transparent 50%);
      padding: 32px 24px;
      text-align: center;
      position: relative;
    }
    
    .header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
      opacity: 0.3;
    }
    
    .brand-container {
      position: relative;
      z-index: 1;
    }
    
    .logo {
      width: 48px;
      height: 48px;
      margin: 0 auto 16px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .header h1 {
      margin: 0;
      font-size: 24px;
      font-weight: 700;
      color: #ffffff;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }
    
    .header .tagline {
      margin: 8px 0 0;
      font-size: 14px;
      color: rgba(255, 255, 255, 0.8);
      font-weight: 500;
    }
    
    /* Content Area */
    .content {
      padding: 32px 24px;
      line-height: 1.6;
      background: rgba(15, 23, 42, 0.2);
    }
    
    .content p {
      margin: 0 0 16px;
      color: #cbd5e1;
    }
    
    .content p:first-child {
      font-size: 18px;
      font-weight: 600;
      color: #e2e8f0;
    }
    
    /* Security Code Display */
    .code-container {
      text-align: center;
      margin: 32px 0;
      padding: 24px;
      background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(6, 182, 212, 0.05) 100%);
      border: 1px solid rgba(59, 130, 246, 0.2);
      border-radius: 12px;
      backdrop-filter: blur(10px);
    }
    
    .code-label {
      font-size: 14px;
      color: #94a3b8;
      margin-bottom: 12px;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .code {
      font-size: 36px;
      letter-spacing: 8px;
      font-weight: 800;
      font-family: 'SF Mono', Monaco, 'Cascadia Code', 'Roboto Mono', Consolas, 'Courier New', monospace;
      color: #60a5fa;
      text-shadow: 0 0 20px rgba(96, 165, 250, 0.3);
      margin: 0;
      line-height: 1.2;
    }
    
    /* Security Warning */
    .security-notice {
      padding: 20px;
      background: rgba(239, 68, 68, 0.1);
      border: 1px solid rgba(239, 68, 68, 0.2);
      border-radius: 8px;
      margin: 24px 0;
    }
    
    .security-notice .icon {
      width: 20px;
      height: 20px;
      display: inline-block;
      vertical-align: middle;
      margin-right: 8px;
    }
    
    .muted {
      color: #94a3b8;
      font-size: 14px;
      line-height: 1.5;
    }
    
    .warning-text {
      color: #fca5a5;
      font-size: 13px;
      margin: 0;
      display: flex;
      align-items: flex-start;
    }
    
    /* Footer */
    .footer {
      text-align: center;
      color: #64748b;
      font-size: 12px;
      padding: 24px;
      background: rgba(15, 23, 42, 0.3);
      border-top: 1px solid rgba(71, 85, 105, 0.2);
    }
    
    .footer p {
      margin: 0;
      line-height: 1.5;
    }
    
    /* Responsive */
    @media (max-width: 480px) {
      .container {
        padding: 20px 10px;
      }
      
      .header {
        padding: 24px 16px;
      }
      
      .content {
        padding: 24px 16px;
      }
      
      .code {
        font-size: 28px;
        letter-spacing: 4px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="card">
      <div class="header">
        <div class="brand-container">
          <div class="logo">
            <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24" style="color: #60a5fa;">
              <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M12,7C13.4,7 14.8,8.6 14.8,10V11.5C15.4,11.5 16,12.1 16,12.7V16.2C16,16.8 15.4,17.3 14.8,17.3H9.2C8.6,17.3 8,16.8 8,16.2V12.7C8,12.1 8.6,11.5 9.2,11.5V10C9.2,8.6 10.6,7 12,7M12,8.2C11.2,8.2 10.5,8.7 10.5,10V11.5H13.5V10C13.5,8.7 12.8,8.2 12,8.2Z"/>
            </svg>
          </div>
          <h1>{{ $appName }}</h1>
          <div class="tagline">Secure Authentication Hub</div>
        </div>
      </div>
      
      <div class="content">
        <p>Security verification required</p>
        <p>Use the verification code below to complete your authentication to <strong>{{ $appName }}</strong>. This code ensures secure access to your account.</p>
        
        <div class="code-container">
          <div class="code-label">Verification Code</div>
          <div class="code">{{ $code }}</div>
        </div>
        
        <div class="security-notice">
          <p class="warning-text">
            <svg class="icon" fill="currentColor" viewBox="0 0 24 24">
              <path d="M13,13H11V7H13M13,17H11V15H13M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/>
            </svg>
            <span>This code will expire in 10 minutes. If you didn't request this verification, please ignore this email and consider securing your account.</span>
          </p>
        </div>
        
        <p class="muted">
          For your security, never share this code with anyone. {{ $appName }} will never ask for your verification code via phone or email.
        </p>
      </div>
      
      <div class="footer">
        <p>© {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
        <p>This is an automated security message.</p>
      </div>
    </div>
  </div>
</body>
</html>