<?php

use Illuminate\Support\Facades\Route;
use Mariojgt\MasterKey\Http\Controllers\QrController;
use Mariojgt\MasterKey\Http\Controllers\WebLoginController;

/*
|--------------------------------------------------------------------------
| MasterKey Web Routes
|--------------------------------------------------------------------------
|
| These routes handle the web-based QR login functionality.
| They are loaded with the 'web' middleware group by default.
|
*/

Route::middleware('web')->group(function () {

    // QR Login Page - displays QR code for mobile app scanning
    Route::get(config('masterkey.routes.login-qr'), [QrController::class, 'loginQrPage'])
        ->name('masterkey.login-qr');

    // Status endpoint for polling login approval status
    Route::get('/api/web/status', [WebLoginController::class, 'status'])
        ->name('masterkey.web-login-status');
});
