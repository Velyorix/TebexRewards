<?php

return [
    'defaults' => [
        'goal_message_reached' => '🎉 Objectif atteint ! Merci à tous ! 🎉',
    ],

    'route_descriptions' => [
        'hub' => 'Tebex Rewards — hub dons',
        'leaderboard_only' => 'Classement des donateurs (table seule)',
    ],

    'nav' => [
        'leaderboard' => 'Classement des donateurs',
        'hub' => 'Hub dons',
        'leaderboard_only' => 'Classement seul',
    ],

    'hub' => [
        'title' => 'Tebex Rewards',
        'intro' => 'Objectif financier, dernier acheteur et classement des donateurs sur une même page.',
        'leaderboard_standalone_cta' => 'Ouvrir la page classement seul',
        'leaderboard_standalone_hint' => 'page adaptée à l’intégration : uniquement le tableau du classement.',
    ],

    'period' => [
        'all' => 'Tout temps',
        'month' => 'Ce mois-ci',
        'week' => 'Cette semaine',
        'day' => 'Aujourd’hui',
    ],

    'leaderboard' => [
        'title' => 'Classement des donateurs',
        'page_standalone_title' => 'Classement des donateurs',
        'back_to_hub' => '← Retour au hub dons',
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
        'sync_forced' => 'Cache API Headless actualisé.',
        'nav' => [
            'settings' => 'Paramètres',
            'leaderboard' => 'Classement',
            'progress' => 'Objectif',
            'ranks' => 'Rangs',
        ],
        'pages' => [
            'leaderboard_hint' => 'Les paramètres du classement se gèrent dans la page Paramètres.',
            'leaderboard_title' => 'Vue classement',
            'leaderboard_intro' => 'Aperçu du classement pour la période choisie, statistiques et liens publics.',
            'leaderboard_settings_btn' => 'Options du classement',
            'goto_leaderboard_admin' => 'Page admin classement',
            'progress_hint' => 'Utilisez la page admin Objectif pour un aperçu en direct, ou modifiez tout dans Paramètres.',
            'ranks_hint' => 'Gérez les paliers (ajout, édition, suppression, réinitialisation) depuis la page Rangs.',
        ],
        'leaderboard' => [
            'stats_unique_donors' => 'Donateurs uniques',
            'stats_transactions' => 'Paiements terminés',
            'stats_volume' => 'Volume (période)',
            'preview_title' => 'Aperçu',
            'preview_footer' => 'Affichage du top :n joueurs selon les paramètres.',
            'links_title' => 'URLs Tebex et intégration',
            'link_hub' => 'Hub public (/tebexrewards)',
            'link_standalone' => 'Page classement seul (/tebexrewards/leaderboard)',
            'link_webhook' => 'Webhook (POST)',
            'link_widget_json' => 'Widget JSON classement',
        ],
        'cache_cleared' => 'Cache Tebex Rewards vidé (classement, widgets).',

        'quick_actions' => [
            'title' => 'Actions rapides',
            'intro' => 'Ces actions s’exécutent tout de suite et ne remplacent pas l’enregistrement des paramètres.',
            'sync_help' => 'Forcer la synchro rafraîchit le cache métadonnées Headless (catégories, packs). N’importe pas l’historique des paiements — les webhooks gèrent les achats en direct.',
        ],

        'actions' => [
            'clear_cache' => 'Vider le cache',
            'clear_cache_help' => 'Supprime les résultats mis en cache du classement, du dernier acheteur et de la barre d’objectif (ne supprime pas les lignes en base tebex_transactions). Utile après une correction ou si les données semblent figées.',
            'copy' => 'Copier',
            'force_sync' => 'Forcer synchro Headless',
        ],

        'settings' => [
            'title' => 'Paramètres',
            'sections' => [
                'general' => 'Général',
                'leaderboard' => 'Classement',
                'goal' => 'Barre de progression d’objectif',
                'last' => 'Dernier acheteur',
            ],
            'tabs_help' => [
                'general' => 'Identifiants API, secret webhook, maintenance et liens du menu membre.',
                'leaderboard' => 'Apparence et rafraîchissement du tableau sur les pages publiques.',
                'goal' => 'Couleurs de la barre d’objectif et boucle éventuelle.',
                'last' => 'Comportement du widget dernier acheteur.',
            ],
            'user_nav_title' => 'Menu utilisateur (navbar)',
            'user_nav_help' => 'Contrôle quelles entrées Tebex Rewards apparaissent dans le menu du compte connecté. Les URLs publiques restent accessibles si on connaît le lien.',
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
            'webhook_endpoint_url' => 'URL du webhook (à coller dans Tebex)',
            'webhook_endpoint_help' => 'Configurez exactement cette URL dans les webhooks du projet Tebex.',
            'sync_interval' => 'Intervalle de synchronisation',
            'sync_interval_help' => 'Fréquence d’exécution planifiée de tebexrewards:sync (cache métadonnées Headless).',
            'maintenance' => 'Mode maintenance (désactive les modules publics)',

            'nav_user_hub' => 'Afficher « Hub dons » dans le menu membre',
            'nav_user_hub_help' => 'Lien vers la page /tebexrewards (objectif, dernier acheteur, classement).',
            'nav_user_leaderboard' => 'Afficher « Classement seul » dans le menu membre',
            'nav_user_leaderboard_help' => 'Lien vers la page /tebexrewards/leaderboard (table seule).',

            'leaderboard_limit' => 'Nombre de joueurs affichés',
            'leaderboard_period' => 'Période par défaut',
            'leaderboard_columns' => 'Colonnes affichées',
            'leaderboard_medals' => 'Afficher les médailles pour le top 3',
            'leaderboard_avatars' => 'Afficher les avatars',
            'leaderboard_poll_seconds' => 'Intervalle de rafraîchissement auto',
            'leaderboard_poll_option' => 'Toutes les :seconds s',
            'leaderboard_poll_help' => 'Fréquence à laquelle le tableau public interroge le widget JSON (mises à jour en direct).',
            'leaderboard_layout' => 'Mise en page du tableau',
            'leaderboard_layout_default' => 'Par défaut (confortable)',
            'leaderboard_layout_compact' => 'Compact (table dense)',
            'leaderboard_layout_help' => 'Équivalent CDC « template » — compact utilise un style de tableau plus serré.',

            'goal_enabled' => 'Activer la barre d’objectif',
            'goal_target' => 'Montant cible',
            'goal_currency' => 'Devise',
            'goal_color_start' => 'Couleur début (#hex)',
            'goal_color_mid' => 'Couleur milieu (#hex)',
            'goal_color_end' => 'Couleur fin (#hex)',
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

        'progress' => [
            'title' => 'Barre d’objectif',
            'intro' => 'Aperçu du rendu sur le hub public. Les réglages complets sont dans Paramètres → Objectif.',
            'open_settings' => 'Modifier dans les paramètres',
            'view_public' => 'Ouvrir le hub public',
            'preview_title' => 'Aperçu en direct',
            'preview_footer' => 'Les montants proviennent des paiements Tebex « terminés » enregistrés sur ce site.',
        ],

        'ranks' => [
            'title' => 'Paliers de rang',
            'intro' => 'Définissez des paliers par montant minimum cumulé. Le plus haut palier atteint s’affiche sur le profil utilisateur (thème Azuriom par défaut).',
            'section_profile' => 'Carte sur le profil',
            'section_profile_help' => 'Si activé, les membres connectés voient leur total donné et leur rang sur la page profil (thème Bootstrap classique). Les thèmes personnalisés peuvent inclure tebexrewards::user.profile_snippet.',
            'profile_card_enabled' => 'Afficher la carte Tebex Rewards sur le profil',
            'link_goal_settings' => 'Objectif & devise',
            'form_add' => 'Ajouter un palier',
            'form_edit' => 'Modifier le palier',
            'cancel_edit' => 'Annuler la modification',
            'table_title' => 'Paliers configurés',
            'actions' => 'Actions',
            'empty' => 'Aucun palier. Ajoutez-en un ou réinitialisez selon le CDC.',
            'reset_defaults' => 'Réinitialiser (défaut CDC)',
            'reset_confirm' => 'Remplacer tous les paliers par les 7 paliers par défaut du cahier des charges ? Les paliers actuels seront supprimés.',
            'delete_confirm' => 'Supprimer ce palier ?',
            'icon_help' => 'Emoji ou URL complète d’image.',
            'created' => 'Palier créé.',
            'updated' => 'Palier mis à jour.',
            'deleted' => 'Palier supprimé.',
            'reset_done' => 'Paliers par défaut restaurés.',
            'fields' => [
                'name' => 'Nom du rang',
                'min_amount' => 'Montant minimum',
                'icon' => 'Icône',
                'color_hex' => 'Couleur du badge (#hex)',
                'enabled' => 'Actif',
            ],
        ],
    ],

    'profile' => [
        'card_title' => 'Dons Tebex',
        'total_label' => 'Total donné (tout temps)',
        'no_tier' => 'Aucun palier de soutien atteint pour le moment.',
        'tier_hint' => 'Le palier le plus élevé auquel vous avez droit est affiché (total sur paiements terminés uniquement).',
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
