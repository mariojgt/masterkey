
<?php

return [
    'token_expires_days' => null, // null => never expire
    'post_login_redirect' => '/admin',
    'logout_callback_route' => '/qr-logout-callback',
    'qr_prefix' => 'mkey:',
];
