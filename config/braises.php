<?php

declare(strict_types=1);

return [
    // Incrémenter à chaque modification de CSS/JS (?v=X.X)
    'asset_version' => env('ASSET_VERSION', '2.2'),

    // URL du site WordPress actuel, source de l'import des contenus
    'wordpress_url' => env('WORDPRESS_URL', 'https://www.braisesemeraude.com'),

    // Verrou de préproduction (laisser vide en production)
    'preprod' => [
        'user' => env('PREPROD_USER'),
        'password' => env('PREPROD_PASSWORD'),
    ],

    // Compte groupe créé par le seeder (lu ici pour survivre à config:cache)
    'admin' => [
        'name' => env('ADMIN_NAME', 'Administrateur'),
        'email' => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),
    ],
];
