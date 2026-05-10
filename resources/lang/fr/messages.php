<?php

return [
    'defaults' => [
        'goal_message_reached' => '🎉 Objectif atteint ! Merci à tous ! 🎉',
    ],

    'nav' => [
        'leaderboard' => 'Classement des donateurs',
    ],

    'period' => [
        'all' => 'Tout temps',
        'month' => 'Ce mois-ci',
        'week' => 'Cette semaine',
        'day' => 'Aujourd’hui',
    ],

    'leaderboard' => [
        'title' => 'Classement des donateurs',
        'redirecting' => 'Redirection…',
        'period' => 'Période',
        'limit' => 'Affichage',
        'limit_locked' => 'Le nombre de joueurs est configuré par le serveur.',
        'empty' => 'Aucune transaction pour le moment.',
        'columns' => [
            'rank' => 'Rang',
            'player' => 'Joueur',
            'amount' => 'Montant total',
            'purchases' => 'Achats',
        ],
    ],

    'actions' => [
        'apply' => 'Appliquer',
    ],

    'goal' => [
        'title' => 'Objectif',
        'percent' => ':percent%',
    ],

    'last' => [
        'title' => 'Dernier acheteur',
        'loading' => 'Chargement…',
        'empty' => 'Aucun achat pour le moment.',
        'spent' => 'a dépensé :amount',
        'on_package' => 'sur « :package »',
        'seconds_ago' => 'il y a :seconds secondes',
        'minutes_ago' => 'il y a :minutes minutes',
        'hours_ago' => 'il y a :hours heures',
        'days_ago' => 'il y a :days jours',
    ],

    'admin' => [
        'title' => 'Tebex Rewards',
        'welcome' => 'Configurez Tebex Rewards depuis la page des paramètres.',
        'nav' => [
            'settings' => 'Paramètres',
            'leaderboard' => 'Classement',
            'progress' => 'Objectif',
            'ranks' => 'Rangs',
        ],
        'pages' => [
            'leaderboard_hint' => 'Les paramètres du classement se gèrent dans la page Paramètres.',
            'progress_hint' => 'Les paramètres de l’objectif se gèrent dans la page Paramètres.',
            'ranks_hint' => 'La gestion des paliers de rang apparaîtra ici une fois les paliers créés.',
        ],
        'settings' => [
            'title' => 'Paramètres',
            'sections' => [
                'general' => 'Général',
                'leaderboard' => 'Classement',
                'goal' => 'Barre de progression d’objectif',
                'last' => 'Dernier acheteur',
            ],
        ],
        'sync_interval' => [
            'minutes' => ':minutes minutes',
        ],
        'timestamps' => [
            'relative' => 'Relatif',
            'absolute' => 'Absolu',
        ],
        'fields' => [
            'tebex_api_key' => 'Clé API Tebex',
            'tebex_api_key_help' => 'Stockée chiffrée dans les paramètres. Requise pour la synchronisation et la validation des webhooks.',
            'webhook_secret' => 'Secret de webhook',
            'webhook_secret_help' => 'Utilisé pour valider les webhooks Tebex (HMAC SHA256 dans X-Signature). Stocké chiffré dans les paramètres.',
            'sync_interval' => 'Intervalle de synchronisation',
            'maintenance' => 'Mode maintenance (désactive les modules publics)',

            'leaderboard_limit' => 'Nombre de joueurs affichés',
            'leaderboard_period' => 'Période par défaut',
            'leaderboard_columns' => 'Colonnes affichées',
            'leaderboard_medals' => 'Afficher les médailles pour le top 3',
            'leaderboard_avatars' => 'Afficher les avatars',

            'goal_enabled' => 'Activer la barre d’objectif',
            'goal_target' => 'Montant cible',
            'goal_currency' => 'Devise',
            'goal_color_start' => 'Couleur début',
            'goal_color_mid' => 'Couleur milieu',
            'goal_color_end' => 'Couleur fin',
            'goal_message_reached' => 'Message objectif atteint',
            'goal_reset_enabled' => 'Activer la remise à zéro / boucle',
            'goal_reset_increment' => 'Incrément automatique après reset',

            'last_enabled' => 'Activer le dernier acheteur',
            'last_show_package' => 'Afficher le nom du pack',
            'last_show_amount' => 'Afficher le montant',
            'last_timestamp' => 'Format de date',
            'last_animation' => 'Activer l’animation',
            'last_animation_speed' => 'Vitesse d’animation (ms)',
        ],
    ],

    'console' => [
        'sync_done' => 'Synchronisation terminée : :synced synchronisées, :skipped ignorées.',
    ],

    'errors' => [
        'missing_api_key' => 'La clé API Tebex est manquante.',
        'invalid_api_key' => 'La clé API Tebex est invalide.',
        'api_request_failed' => 'La requête Tebex a échoué (HTTP :status).',
    ],

    'webhook' => [
        'ok' => 'Webhook accepté.',
        'invalid_signature' => 'Signature invalide.',
        'invalid_payload' => 'Payload invalide.',
        'missing_secret' => 'Le secret de webhook est manquant.',
    ],
];

