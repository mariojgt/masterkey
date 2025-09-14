
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MasterKey - Login via QR</title>
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; display: grid; place-items: center; min-height: 100vh; background:#fafafa; }
    .card { padding: 24px; border: 1px solid #eee; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); background:white; }
    #qr { width: 256px; height: 256px; margin-bottom: 8px; }
    .muted { color: #666; font-size: 14px; }
    .ok { color: #0a8; }
  </style>
</head>
<body>
  <div class="card">
    <h2>Scan to log in</h2>
    <div id="qr"></div>
    <p class="muted">Open the MasterKey app and scan this code.</p>
    <p id="status" class="muted">Waiting for approval...</p>
  </div>
  <!-- Try multiple CDN sources for QR code library -->
  <script src="https://unpkg.com/qrcode@1.5.4/build/qrcode.min.js" onerror="loadQRCodeFallback()"></script>
  <script>
    const payload = @json($payload);
    const statusUrl = @json($statusUrl);
    let qrCodeLoaded = false;

    // Function to load fallback CDN
    function loadQRCodeFallback() {
      const script = document.createElement('script');
      script.src = 'https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js';
      script.onload = function() {
        generateQRWithFallback();
      };
      script.onerror = function() {
        generateQRWithAPI();
      };
      document.head.appendChild(script);
    }

    // Generate QR with fallback library (different API)
    function generateQRWithFallback() {
      try {
        const qr = qrcode(0, 'M');
        qr.addData(payload);
        qr.make();
        document.getElementById('qr').innerHTML = qr.createImgTag(4);
        const img = document.querySelector('#qr img');
        if (img) {
          img.style.width = '256px';
          img.style.height = '256px';
        }
      } catch (e) {
        generateQRWithAPI();
      }
    }

    // Generate QR using external API as last resort
    function generateQRWithAPI() {
      const qrDiv = document.getElementById('qr');
      const img = document.createElement('img');
      img.src = `https://api.qrserver.com/v1/create-qr-code/?size=256x256&data=${encodeURIComponent(payload)}`;
      img.width = 256;
      img.height = 256;
      img.style.border = '1px solid #ddd';
      img.alt = 'QR Code';
      qrDiv.innerHTML = '';
      qrDiv.appendChild(img);
    }

    // Main QR generation function
    function generateQR() {
      try {
        if (typeof QRCode !== 'undefined') {
          QRCode.toCanvas(document.getElementById('qr'), payload, { width: 256 });
          qrCodeLoaded = true;
        } else {
          throw new Error('QRCode library not loaded');
        }
      } catch (e) {
        generateQRWithAPI();
      }
    }

    // Wait for DOM and try to generate QR
    document.addEventListener('DOMContentLoaded', function() {
      // Small delay to ensure script loading
      setTimeout(generateQR, 100);
    });

    async function poll() {
      try {
        const res = await fetch(statusUrl);
        const data = await res.json();
        if (data.status === 'used' && data.redirect) {
          document.getElementById('status').textContent = 'Approved! Redirecting...';
          document.getElementById('status').className = 'ok';
          window.location.href = data.redirect;
          return;
        }
      } catch (e) {}
      setTimeout(poll, 1200);
    }
    poll();
  </script>
</body>
</html>
