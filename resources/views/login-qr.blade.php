
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MasterKey - Secure QR Login</title>
  <style>
    /* Brand Guidelines Compliant QR Login Page */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
      background-attachment: fixed;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #e2e8f0;
      position: relative;
      overflow-x: hidden;
    }
    
    /* Enterprise Background Pattern */
    body::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: 
        radial-gradient(circle at 20% 50%, rgba(59, 130, 246, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(6, 182, 212, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 40% 80%, rgba(59, 130, 246, 0.05) 0%, transparent 50%);
      z-index: 1;
    }
    
    /* Grid Pattern */
    body::after {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M 20 0 L 0 0 0 20" fill="none" stroke="rgba(59,130,246,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
      opacity: 0.4;
      z-index: 2;
    }
    
    .container {
      position: relative;
      z-index: 10;
      width: 100%;
      max-width: 480px;
      margin: 0 auto;
      padding: 20px;
    }
    
    /* Glassmorphism Card */
    .card {
      background: rgba(30, 41, 59, 0.4);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(59, 130, 246, 0.2);
      border-radius: 24px;
      box-shadow: 
        0 25px 50px -12px rgba(0, 0, 0, 0.25),
        0 0 0 1px rgba(255, 255, 255, 0.05);
      padding: 40px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    
    .card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.5), transparent);
    }
    
    /* Brand Header */
    .brand-header {
      margin-bottom: 32px;
    }
    
    .logo-container {
      width: 64px;
      height: 64px;
      margin: 0 auto 16px;
      background: rgba(59, 130, 246, 0.1);
      border: 1px solid rgba(59, 130, 246, 0.2);
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      backdrop-filter: blur(10px);
      position: relative;
    }
    
    .logo-container::before {
      content: '';
      position: absolute;
      inset: -1px;
      background: linear-gradient(135deg, #3b82f6, #06b6d4);
      border-radius: 16px;
      opacity: 0.2;
      z-index: -1;
    }
    
    .logo-container svg {
      color: #60a5fa;
      filter: drop-shadow(0 0 20px rgba(96, 165, 250, 0.3));
    }
    
    .brand-title {
      font-size: 28px;
      font-weight: 700;
      color: #e2e8f0;
      margin: 0 0 8px;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }
    
    .brand-tagline {
      font-size: 14px;
      color: #94a3b8;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    /* QR Code Section */
    .qr-section {
      margin: 32px 0;
    }
    
    .qr-title {
      font-size: 20px;
      font-weight: 600;
      color: #e2e8f0;
      margin-bottom: 8px;
    }
    
    .qr-subtitle {
      font-size: 14px;
      color: #94a3b8;
      margin-bottom: 24px;
      line-height: 1.5;
    }
    
    .qr-container {
      width: 280px;
      height: 280px;
      margin: 0 auto;
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 1) 100%);
      border-radius: 20px;
      padding: 20px;
      box-shadow: 
        0 20px 25px -5px rgba(0, 0, 0, 0.1),
        0 10px 10px -5px rgba(0, 0, 0, 0.04),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
      position: relative;
    }
    
    .qr-container::before {
      content: '';
      position: absolute;
      inset: -2px;
      background: linear-gradient(135deg, #3b82f6, #06b6d4);
      border-radius: 22px;
      z-index: -1;
      opacity: 0.6;
    }
    
    #qr {
      width: 100%;
      height: 100%;
      border-radius: 12px;
      overflow: hidden;
    }
    
    #qr svg {
      width: 100%;
      height: 100%;
    }
    
    /* Status Section */
    .status-section {
      margin-top: 32px;
    }
    
    .status-container {
      padding: 16px 20px;
      background: rgba(15, 23, 42, 0.3);
      border: 1px solid rgba(71, 85, 105, 0.3);
      border-radius: 12px;
      margin: 16px 0;
    }
    
    .status-icon {
      width: 24px;
      height: 24px;
      margin: 0 auto 8px;
      color: #06b6d4;
      animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.5; }
    }
    
    #status {
      font-size: 16px;
      font-weight: 500;
      color: #cbd5e1;
      margin: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }
    
    #status.success {
      color: #4ade80;
    }
    
    #status.success .status-icon {
      color: #4ade80;
      animation: none;
    }
    
    /* Instructions */
    .instructions {
      margin-top: 24px;
      padding: 20px;
      background: rgba(59, 130, 246, 0.05);
      border: 1px solid rgba(59, 130, 246, 0.1);
      border-radius: 12px;
    }
    
    .instructions-title {
      font-size: 14px;
      font-weight: 600;
      color: #60a5fa;
      margin-bottom: 8px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .instructions-text {
      font-size: 13px;
      color: #94a3b8;
      line-height: 1.5;
      margin: 0;
    }
    
    /* Security Badge */
    .security-badge {
      position: absolute;
      top: 16px;
      right: 16px;
      background: rgba(34, 197, 94, 0.1);
      border: 1px solid rgba(34, 197, 94, 0.2);
      border-radius: 8px;
      padding: 4px 8px;
      font-size: 11px;
      color: #4ade80;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    /* Responsive */
    @media (max-width: 480px) {
      .container {
        padding: 16px;
      }
      
      .card {
        padding: 24px;
        border-radius: 16px;
      }
      
      .qr-container {
        width: 240px;
        height: 240px;
        padding: 16px;
      }
      
      .brand-title {
        font-size: 24px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="card">
      <div class="security-badge">
        <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24" style="display: inline; margin-right: 4px;">
          <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1Z"/>
        </svg>
        Secure
      </div>
      
      <div class="brand-header">
        <div class="logo-container">
          <svg width="32" height="32" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M12,7C13.4,7 14.8,8.6 14.8,10V11.5C15.4,11.5 16,12.1 16,12.7V16.2C16,16.8 15.4,17.3 14.8,17.3H9.2C8.6,17.3 8,16.8 8,16.2V12.7C8,12.1 8.6,11.5 9.2,11.5V10C9.2,8.6 10.6,7 12,7M12,8.2C11.2,8.2 10.5,8.7 10.5,10V11.5H13.5V10C13.5,8.7 12.8,8.2 12,8.2Z"/>
          </svg>
        </div>
        <h1 class="brand-title">MasterKey</h1>
        <div class="brand-tagline">Secure Authentication Hub</div>
      </div>
      
      <div class="qr-section">
        <h2 class="qr-title">Scan to Sign In</h2>
        <p class="qr-subtitle">Use your MasterKey mobile app to scan this QR code and authenticate securely.</p>
        
        <div class="qr-container">
          <div id="qr">{!! $qrSvg !!}</div>
        </div>
      </div>
      
      <div class="status-section">
        <div class="status-container">
          <div class="status-icon">
            <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,6A6,6 0 0,0 6,12A6,6 0 0,0 12,18A6,6 0 0,0 18,12A6,6 0 0,0 12,6M10,16.5L6,12.5L7.5,11L10,13.5L16.5,7L18,8.5L10,16.5Z"/>
            </svg>
          </div>
          <p id="status">Waiting for scan...</p>
        </div>
      </div>
      
      <div class="instructions">
        <div class="instructions-title">How to proceed</div>
        <p class="instructions-text">
          1. Open your MasterKey mobile application<br>
          2. Tap the scan icon or QR scanner<br>
          3. Point your camera at the QR code above<br>
          4. Approve the login request when prompted
        </p>
      </div>
    </div>
  </div>

  <script>
    const statusUrl = @json($statusUrl);
    const statusElement = document.getElementById('status');
    const statusIcon = document.querySelector('.status-icon');
    
    async function poll() {
      try {
        const res = await fetch(statusUrl);
        const data = await res.json();
        
        if (data.status === 'used' && data.redirect) {
          statusElement.textContent = 'Authentication approved! Redirecting...';
          statusElement.className = 'success';
          statusIcon.innerHTML = `
            <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M11,16.5L6.5,12L7.91,10.59L11,13.67L16.59,8.09L18,9.5L11,16.5Z"/>
            </svg>
          `;
          
          setTimeout(() => {
            window.location.href = data.redirect;
          }, 1500);
          return;
        }
        
        if (data.status === 'pending') {
          statusElement.textContent = 'QR code scanned, waiting for approval...';
        }
        
      } catch (e) {
        console.error('Polling error:', e);
      }
      
      setTimeout(poll, 2000);
    }
    
    // Start polling
    poll();
    
    // Add some visual feedback for the QR code container
    document.querySelector('.qr-container').addEventListener('mouseover', function() {
      this.style.transform = 'scale(1.02)';
      this.style.transition = 'transform 0.2s ease';
    });
    
    document.querySelector('.qr-container').addEventListener('mouseout', function() {
      this.style.transform = 'scale(1)';
    });
  </script>
</body>
</html>
