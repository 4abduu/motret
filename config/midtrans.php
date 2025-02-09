<?php

return [
    // API Key Midtrans
    'merchant_id'   => env('MIDTRANS_MERCHANT_ID', ''),
    'server_key'    => env('MIDTRANS_SERVER_KEY', ''),
    'client_key'    => env('MIDTRANS_CLIENT_KEY', ''),

    // Mode Production atau Sandbox
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    // Keamanan tambahan
    'is_sanitized'  => env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds'        => env('MIDTRANS_IS_3DS', true),

    // URL Callback (Opsional, bisa diatur di dashboard Midtrans)
    'notification_url' => env('MIDTRANS_NOTIFICATION_URL', ''),
    'finish_redirect_url' => env('MIDTRANS_FINISH_REDIRECT_URL', ''),
    'error_redirect_url' => env('MIDTRANS_ERROR_REDIRECT_URL', ''),
];
