
<?php

return [
    'token_expires_days' => null, // null => never expire
    'post_login_redirect' => '/admin',
    'logout_callback_route' => '/qr-logout-callback',
    'qr_prefix' => 'mkey:',
    // Optional fully-qualified class name implementing a hook handler.
    // Example: App\MasterKey\Handler::class
        // The handler should have: public function handleMasterKey(string $hook, object $context = null)
    'handler' => App\Helpers\MasterKeyHandler::class
];
