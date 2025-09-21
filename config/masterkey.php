
<?php

return [
    'token_expires_days' => null, // null => never expire
    'post_login_redirect' => '/admin/login',
    'logout_callback_route' => '/qr-logout-callback',
    'qr_prefix' => 'mkey:',

    'routes' => [
        'login-qr' => '/login-qr'
    ],
    // Safety setting: allow automatic user creation in production
    // Set to true if you want to enable automatic user creation even in production
    // Recommended: implement user creation in your MasterKeyHandler instead
    'allow_auto_user_creation' => false,

    // Optional fully-qualified class name implementing a hook handler.
    // Example: App\MasterKey\Handler::class
        // The handler should have: public function handleMasterKey(string $hook, object $context = null)
    'handler' => App\Helpers\MasterKeyHandler::class
];
