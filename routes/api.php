<?php

use Illuminate\Support\Facades\Route;
use Mariojgt\MasterKey\Http\Controllers\AppAuthController;
use Mariojgt\MasterKey\Http\Controllers\WebLoginController;
use Mariojgt\MasterKey\Http\Controllers\LogoutController;

/*
|--------------------------------------------------------------------------
| MasterKey API Routes
|--------------------------------------------------------------------------
|
| These routes handle the API endpoints for mobile app authentication
| and web login approval functionality.
|
| CORS is completely disabled for all these routes to allow cross-origin access.
|
*/

// Public API endpoints - no authentication required
Route::prefix('api')
    ->group(function () {

        // Mobile app authentication endpoints
        Route::post('/app/request-code', [AppAuthController::class, 'requestCode'])
            ->name('masterkey.app.request-code');

        Route::post('/app/verify', [AppAuthController::class, 'verify'])
            ->name('masterkey.app.verify');

        // Protected API endpoints - require Bearer token authentication
        Route::middleware(\Mariojgt\MasterKey\Http\Middleware\AuthToken::class)->group(function () {

            // Web login approval - called by authenticated mobile app
            Route::post('/web/approve', [WebLoginController::class, 'approve'])
                ->name('masterkey.web.approve');

            // Logout QR - triggers logout event/callback
            Route::post('/logout-qr', [LogoutController::class, 'logoutQr'])
                ->name('masterkey.logout-qr');
        });
    });
