<?php

return [
    'mode' => env('PAYPAL_MODE', 'sandbox'), // 'sandbox' or 'live'
    'client_id' => env('PAYPAL_SANDBOX_CLIENT_ID', ''),
    'secret' => env('PAYPAL_SANDBOX_CLIENT_SECRET', ''),
    'payment_action' => 'Sale',
    'currency' => 'USD',
    'notify_url' => '', // Optional: used for IPN notifications
    'certificate' => storage_path('paypal_cert.pem'), // Optional
];