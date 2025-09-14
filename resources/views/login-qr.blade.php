
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
  <div id="qr">{!! $qrSvg !!}</div>
    <p class="muted">Open the MasterKey app and scan this code.</p>
    <p id="status" class="muted">Waiting for approval...</p>
  </div>
  <script>
    const statusUrl = @json($statusUrl);
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
  setTimeout(poll, 2000);
    }
    poll();
  </script>
</body>
</html>
