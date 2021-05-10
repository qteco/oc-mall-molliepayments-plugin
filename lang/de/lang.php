<?php
return [
    'plugin' => [
        'name' => 'Mollie Zahlungen für Mall',
        'description' => 'Mollie Zahlungsanbieter für die OFFLINE.Mall Plugin'
    ],
    'settings' => [
        'mollie_mode' => 'Modus',
        'mollie_mode_label' => 'Warnung: Im Live-Modus werden echte Zahlungen verarbeitet',
        'test_api_key' => 'Mollie test API-Schlüssel',
        'test_api_key_label' => 'Sieht aus wie "test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"',
        'live_api_key' => 'Mollie live API-Schlüssel',
        'live_api_key_label' => 'Sieht aus wie "live_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"',
        'orders_page' => 'Ihre Bestellseite',
        'orders_page_label' => 'Kunden werden nach dem Auschecken auf diese Seite weitergeleitet, Beispiel: http://yourwebsite.com/account/orders',
    ],
    'messages' => [
        'order_number' => 'Bestellung #',
        'payment_paid' => 'Die Zahlung wurde erfolgreich abgeschlossen.',
        'payment_failed' => 'Die Zahlung wurde nicht erfolgreich abgeschlossen.',
        'payment_canceled' => 'Die Zahlung wurde annulliert.',
        'payment_expired' => 'Die Zahlungsseite wurde abgebrochen und die Zahlung ist abgelaufen.',
    ],
];
