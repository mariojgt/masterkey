
# MasterKey (Laravel package)

QR-based login bridge between your Laravel app and the MasterKey mobile app (Ionic). **No Sanctum.** Minimal and production-ready.

## Highlights
- Permanent or expiring API tokens (config).
- Pre-login QR → 6-digit code → app token.
- Web/admin login via QR scanned by authenticated app.
- Logout QR triggers event/callback for your custom logic.
- Tiny `auth.token` middleware for Bearer tokens.

## Install
```bash
composer require mariojgt/masterkey:*
php artisan vendor:publish --provider="Mariojgt\MasterKey\MasterKeyServiceProvider" --tag=config
php artisan migrate
```

Add a path repo if using from source:
```json
"repositories": [{ "type": "path", "url": "packages/masterkey" }]
```

## Config (`config/masterkey.php`)
- `token_expires_days` → null = never expire (default).
- `post_login_redirect` → where browser goes after QR approval.
- `logout_callback_route` → optional HTTP callback you implement.
- `qr_prefix` → default `mkey:`.

## Routes
- Public:
  - `GET /login-qr` → Blade page that displays QR and polls status.
  - `GET /api/web/status?session_id=` → polled by the page.
  - `POST /api/app/request-code` `{email}` → returns `{nonce, code, qr_payload}` (demo returns code).
  - `POST /api/app/verify` `{nonce, code}` → returns `{token, user}`.
- Authenticated (with Bearer token via app):
  - `POST /api/web/approve` `{session_id}` → approves web login.
  - `POST /api/logout-qr` → fires event for your logout logic.

## Protect API
```php
Route::middleware(\Mariojgt\MasterKey\Http\Middleware\AuthToken::class)
     ->get('/api/me', fn() => auth()->user());
```

## Client app
You can download here [MasterKey app](https://github.com/mariojgt/masterkey-app).
