<?php
return [
    "plugin" => [
        "name" => "Mollie Zahlungen für Mall",
        "description" => "Mollie Zahlungsanbieter für die OFFLINE.Mall Plugin",
    ],
    "settings" => [
        "mollie_mode" => "Modus",
        "mollie_mode_label" => "Warnung: Im Live-Modus werden echte Zahlungen verarbeitet",
        "test_api_key" => "Mollie test API-Schlüssel",
        "test_api_key_label" => 'Sieht aus wie "test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"',
        "live_api_key" => "Mollie live API-Schlüssel",
        "live_api_key_label" => 'Sieht aus wie "live_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"',
        "orders_page" => "Ihre Bestellseite",
        "orders_page_label" =>
            "Kunden werden nach dem Auschecken auf diese Seite weitergeleitet, Beispiel: http://yourwebsite.com/account/orders",
    ],
];
