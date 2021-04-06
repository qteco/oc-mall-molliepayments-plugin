<?php
return [
    'plugin' => [
        'name' => 'Mollie paiements pour Mall',
        'description' => 'Mollie moyen de paiement pour le plugin OFFLINE.Mall'
    ],
    'settings' => [
        'mollie_mode' => 'Mode',
        'mollie_mode_label' => 'Attention: Le mode en direct traitera les paiements réels',
        'test_api_key' => 'Clé API de test de Mollie',
        'test_api_key_label' => 'Ressemble à "test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"',
        'live_api_key' => 'Clé API de live de Mollie',
        'live_api_key_label' => 'Ressemble à "live_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"',
    ],
    'messages' => [
        'order_number' => 'Commande #',
    ]
];
