# MasterKey Routes Documentation

This package now uses organized route files for better maintainability and clarity.

## Route Files Structure

```
routes/
├── web.php     # Web-based routes with 'web' middleware
└── api.php     # API routes for mobile app communication
```

## Route Organization

### Web Routes (`routes/web.php`)
These routes handle browser-based interactions and use the `web` middleware group:

- `GET /login-qr` - Displays the QR code login page
- `GET /api/web/status` - Status polling endpoint for the QR login page

### API Routes (`routes/api.php`)
These routes handle mobile app API communication:

**Public Routes (no authentication required):**
- `POST /api/app/request-code` - Mobile app requests verification code
- `POST /api/app/verify` - Mobile app verifies code and gets token

**Protected Routes (Bearer token required):**
- `POST /api/web/approve` - Mobile app approves web login
- `POST /api/logout-qr` - Handles logout QR functionality

## Route Names

All routes now use the `masterkey.` prefix for better namespacing:

- `masterkey.login-qr`
- `masterkey.web-login-status`
- `masterkey.app.request-code`
- `masterkey.app.verify`
- `masterkey.web.approve`
- `masterkey.logout-qr`

## Middleware Usage

- **Web routes**: Use Laravel's `web` middleware group (sessions, CSRF, etc.)
- **API routes**: Use custom `AuthToken` middleware for protected endpoints

## Benefits of This Organization

1. **Separation of Concerns**: Web and API routes are clearly separated
2. **Better Documentation**: Each route file has clear comments explaining purpose
3. **Easier Maintenance**: Routes are grouped logically by function
4. **Cleaner Service Provider**: Route registration is simplified
5. **Named Routes**: All routes have descriptive names for easier referencing
