# Tebex Rewards (Azuriom)

Donor leaderboard, financial goal bar, last-purchaser widget, and Tebex webhook integration for [Azuriom](https://azuriom.com/) v1.x.

---

## English

### Requirements

- **Azuriom** 1.0+ (tested with API `1.1.0` as declared in `plugin.json`)
- **PHP** 8.1+ (align with your Azuriom version; 8.2+ for recent Azuriom 1.x)
- **Tebex** project with **Headless / webstore API** (public key) for optional sync
- **Database** supported by Azuriom (MySQL, MariaDB, or SQLite in dev)

### Installation

1. Copy the `tebexrewards` folder into your Azuriom `plugins/` directory (or install from a release package that contains this structure).
2. In the Azuriom **admin panel → Extensions → Plugins**, **enable** *Tebex Rewards*. Migrations run on enable.

### First-time configuration

Open **Admin → Tebex Rewards → Settings** (or **Paramètres** in French).

| Setting | Purpose |
|--------|---------|
| **Tebex API key** | Public key from the Tebex headless / webstore API. Used to validate the key and for the `tebexrewards:sync` task. |
| **Webhook secret** | Secret shown in the Tebex dashboard for **HMAC** signing of webhooks. Stored **encrypted** in Azuriom settings. |
| **Sync interval** | How often the scheduled `tebexrewards:sync` command runs (cache warm / token check). |
| **Maintenance** | When enabled, public routes and JSON widgets return **404** (useful during maintenance). |
| **Leaderboard** | Default limit, period, columns, medals, avatars. |
| **Goal** | Target amount, currency, colours, optional reset / increment. |
| **Last purchaser** | Enable/disable, package name, amount, timestamp format, animation. |

Dedicated admin pages (same menu **Tebex Rewards**): **Leaderboard** (stats + preview + URLs), **Goal progress** (live preview + link to settings), **Ranks** (CRUD tiers, reset to CDC defaults, profile card toggle).

Use **Clear cache** if displayed values look stale after fixing data or changing settings.

### Supporter ranks & user profile

- Configure tiers under **Admin → Tebex Rewards → Ranks** (minimum amount, emoji or image URL, badge colour, active flag).
- **Reset to CDC defaults** restores the seven example tiers from the specification.
- **Show Tebex Rewards card on user profile** adds a card on the default Azuriom profile view (`profile.index`) with lifetime total and highest matching tier (matched by `player_name` = account name and/or `player_uuid` = `game_id` on completed transactions).
- **Custom themes** that replace the profile layout can embed `@includeWhen(plugins()->isEnabled('tebexrewards'), 'tebexrewards::user.profile_snippet', ['user' => $user])` where you want the same block.

### Tebex webhook

Configure Tebex to send webhooks to:

```text
https://YOUR-DOMAIN/api/tebexrewards/webhook
```

- Method: **POST**
- Body: JSON (as sent by Tebex)
- Signature: Tebex signs the payload; this plugin reads **`X-Signature`** or **`X-Tebex-Signature`** (optional `sha256=` prefix), using the same scheme as Tebex’s docs (HMAC-SHA256 over the SHA-256 hash of the raw body).

On **Validation** (`type`: `validation.webhook`), the endpoint must respond with **HTTP 200** and JSON `{"id":"<webhook id>"}` — this plugin does that automatically when the signature is valid.

Keep your **webhook secret** identical in Tebex and in Azuriom admin.

### Public URLs

| Page / widget | URL (relative to site root) |
|---------------|-----------------------------|
| Donations hub (goal + last purchaser + leaderboard) | `/tebexrewards` |
| Leaderboard-only page (table embed) | `/tebexrewards/leaderboard` |
| Last purchaser (JSON) | `GET /api/tebexrewards/widgets/last-purchaser` |
| Leaderboard (JSON, polling) | `GET /api/tebexrewards/widgets/leaderboard?period=all` (`period`: `all`, `month`, `week`, `day`) |

The leaderboard page includes optional client-side refresh for the table and uses the JSON endpoint above.

### Scheduler (sync)

The plugin registers an Artisan command:

```bash
php artisan tebexrewards:sync
```

Interval follows **Sync interval** in admin (Laravel scheduler must run, e.g. cron `* * * * * php artisan schedule:run`).

### Troubleshooting

| Issue | What to check |
|-------|----------------|
| Webhook **401** | Wrong secret; header name; body altered by a proxy (signature is over **raw** JSON). |
| Webhook **422** missing secret | Save the webhook secret in admin (encrypted field non-empty). |
| Empty leaderboard | No completed transactions yet; confirm webhooks are delivered or data exists in `tebex_transactions`. |
| Widgets **404** | Maintenance mode enabled in plugin settings. |
| Stale rankings | Use **Clear cache** or wait for TTL; webhooks invalidate caches when a payment is stored. |

---

## Français

### Prérequis

- **Azuriom** 1.0+ (API déclarée dans `plugin.json`, ex. `1.1.0`)
- **PHP** 8.1+ (suivre la version requise par votre installation Azuriom)
- Projet **Tebex** avec API **Headless / boutique** (clé publique) pour la synchro optionnelle
- **Base de données** prise en charge par Azuriom

### Installation

1. Placez le dossier `tebexrewards` dans le répertoire `plugins/` d’Azuriom (ou installez depuis une archive qui reprend cette arborescence).
2. Dans l’**administration Azuriom → Extensions → Plugins**, **activez** *Tebex Rewards*. Les migrations s’exécutent à l’activation.

### Configuration initiale

Menu **Admin → Tebex Rewards → Paramètres**.

| Réglage | Rôle |
|---------|------|
| **Clé API Tebex** | Clé publique API Headless / boutique. Sert à valider la clé et à la commande `tebexrewards:sync`. |
| **Secret de webhook** | Secret affiché dans Tebex pour la signature **HMAC**. Stocké **chiffré** dans Azuriom. |
| **Intervalle de synchro** | Fréquence de la tâche planifiée `tebexrewards:sync`. |
| **Maintenance** | Si activé, les pages publiques et widgets JSON répondent **404**. |
| **Classement** | Limite, période par défaut, colonnes, médailles, avatars. |
| **Objectif** | Montant cible, devise, couleurs, option reset / incrément. |
| **Dernier acheteur** | Activation, affichage pack / montant / date, animation. |

Autres pages admin (**Tebex Rewards**) : **Classement** (stats + aperçu + liens), **Objectif** (aperçu barre + lien vers paramètres), **Rangs** (CRUD paliers, réinit. CDC, option carte profil).

Utilisez **Vider le cache** si les valeurs affichées semblent figées après une correction ou un changement de configuration.

### Paliers de rang & profil

- Gestion sous **Admin → Tebex Rewards → Rangs** (montant minimum, emoji ou URL d’image, couleur du badge, actif).
- **Réinitialiser (défaut CDC)** restaure les sept paliers d’exemple du cahier des charges.
- L’option **Afficher la carte sur le profil** ajoute une carte sur le profil Azuriom par défaut (`profile.index`) : total des dons et palier le plus élevé (transactions « complete », correspondance par pseudo et/ou `game_id`).
- Les **thèmes personnalisés** peuvent inclure `@includeWhen(plugins()->isEnabled('tebexrewards'), 'tebexrewards::user.profile_snippet', ['user' => $user])` à l’endroit souhaité.

### Webhook Tebex

URL à renseigner dans le tableau de bord Tebex :

```text
https://VOTRE-DOMAINE/api/tebexrewards/webhook
```

- Méthode : **POST**, corps JSON tel qu’envoyé par Tebex.
- Signature : en-têtes **`X-Signature`** ou **`X-Tebex-Signature`** (préfixe `sha256=` accepté), conforme à la documentation Tebex (HMAC-SHA256 sur le hash SHA-256 du corps brut).

Pour la **validation** du endpoint (`validation.webhook`), la réponse doit être **200** avec `{"id":"<id du webhook>"}` — géré automatiquement si la signature est valide.

Le **secret** doit être identique entre Tebex et les paramètres Azuriom.

### URLs publiques

| Élément | URL (relative à la racine du site) |
|---------|-------------------------------------|
| Hub dons (objectif + dernier acheteur + classement) | `/tebexrewards` |
| Page classement seul (table, intégration) | `/tebexrewards/leaderboard` |
| Dernier acheteur (JSON) | `GET /api/tebexrewards/widgets/last-purchaser` |
| Classement (JSON) | `GET /api/tebexrewards/widgets/leaderboard?period=all` (`period` : `all`, `month`, `week`, `day`) |

### Planification (synchro)

```bash
php artisan tebexrewards:sync
```

L’intervalle suit le réglage admin ; le **scheduler Laravel** doit tourner (ex. cron `* * * * * php artisan schedule:run`).

### Dépannage

| Problème | Vérifications |
|----------|----------------|
| Webhook **401** | Secret incorrect ; en-tête ; corps JSON modifié par un proxy (signature sur le corps **brut**). |
| Webhook **422** secret manquant | Enregistrer le secret webhook dans l’admin. |
| Classement vide | Pas encore de transactions « complete » ; vérifier la réception des webhooks ou les données en base. |
| Widgets **404** | Mode maintenance activé. |
| Données dépassées | **Vider le cache** ; les webhooks invalident le cache à chaque achat enregistré. |

---

## License

Same as the parent Azuriom project / plugin distribution unless otherwise stated by Velyorix.
