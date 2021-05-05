<?php
return [
    'plugin' => [
        'name' => 'Mollie Payments for Mall',
        'description' => 'Mollie payment provider for the OFFLINE.Mall plugin'
    ],
    'settings' => [
        'mollie_mode' => 'Mode',
        'mollie_mode_label' => 'Warning: Live mode will process real payments',
        'test_api_key' => 'Mollie test API key',
        'test_api_key_label' => 'Looks like "test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"',
        'live_api_key' => 'Mollie live API key',
        'live_api_key_label' => 'Looks like "live_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"',
    ],
    'messages' => [
        'order_number' => 'Order #',
        'payment_paid' => 'The payment was successfully completed.',
        'payment_failed' => 'The payment has not been successfully completed.',
        'payment_canceled' => 'The payment has been canceled.',
        'payment_expired' => 'The payment page has been abandoned and the payment window has expired.',
    ],
];
