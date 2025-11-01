# 🛠️ COMMANDES UTILES - MYLMS

## Référence rapide de toutes les commandes du projet

---

## 🔧 **MAINTENANCE**

### **Nettoyer les caches**

```bash
# Tout nettoyer (recommandé)
php artisan optimize:clear

# Ou individuellement :
php artisan config:clear    # Configuration
php artisan route:clear     # Routes
php artisan view:clear      # Vues compilées
php artisan cache:clear     # Cache application
php artisan event:clear     # Events
```

---

## 📊 **ANALYTICS**

### **Mettre à jour GeoIP**

```bash
# Télécharger et installer MaxMind GeoLite2-City
php artisan geoip:update
```

### **Vérifier les données analytics**

```bash
php artisan tinker

# Derniers visiteurs
\Modules\LMS\Models\Analytics\UserAnalytics::latest()->take(5)->get();

# Pages vues aujourd'hui
\Modules\LMS\Models\Analytics\PageView::whereDate('visited_at', today())->count();

# Sessions actives
\Modules\LMS\Models\Analytics\UserSession::whereNull('ended_at')->count();
```

---

## ⏰ **SCHEDULER**

### **Lister les tâches planifiées**

```bash
php artisan schedule:list
```

**Résultat attendu** :
```
┌────────────────────────────────────────────────────────┐
│ Command                      Next Due                  │
├────────────────────────────────────────────────────────┤
│ queue:work --sleep=3 ...     48 seconds from now       │
│ geoip:update                 2 days from now           │
│ cleanup-old-analytics        2 days from now           │
└────────────────────────────────────────────────────────┘
```

### **Exécuter le scheduler manuellement**

```bash
php artisan schedule:run
```

### **Tester une tâche spécifique**

```bash
# Tester GeoIP update
php artisan geoip:update

# Tester queue worker
php artisan queue:work --once
```

---

## 🔐 **SESSION UNIQUE**

### **Vérifier les tokens**

```bash
php artisan tinker

# Voir le token d'un utilisateur
$user = \Modules\LMS\Models\User::find(53);
echo "Token : " . $user->session_token;

# Voir tous les utilisateurs connectés (avec token)
\Modules\LMS\Models\User::whereNotNull('session_token')->count();
```

### **Réinitialiser les tokens (déconnecter tout le monde)**

```bash
php artisan tinker

# Réinitialiser tous les tokens users
\Modules\LMS\Models\User::whereNotNull('session_token')->update(['session_token' => null]);

# Réinitialiser tous les tokens admins
\Modules\LMS\Models\Auth\Admin::whereNotNull('session_token')->update(['session_token' => null]);

echo "✅ Tous les utilisateurs seront déconnectés à leur prochaine action";
```

---

## 🗄️ **BASE DE DONNÉES**

### **Migrations**

```bash
# Exécuter toutes les migrations
php artisan migrate

# Exécuter une migration spécifique
php artisan migrate --path=database/migrations/2025_10_29_124047_create_user_analytics_table.php

# Rollback (annuler la dernière migration)
php artisan migrate:rollback

# Voir le statut des migrations
php artisan migrate:status
```

### **Tinker (Console PHP interactive)**

```bash
php artisan tinker

# Exemples utiles :
\DB::table('user_analytics')->count();
\Modules\LMS\Models\User::count();
\Schema::hasTable('user_analytics'); // true
```

---

## 🌐 **ROUTES**

### **Lister toutes les routes**

```bash
php artisan route:list
```

### **Chercher une route spécifique**

```bash
# Windows PowerShell
php artisan route:list | Select-String "analytics"

# Ou en PHP
php artisan route:list --path=analytics
```

### **Nettoyer le cache des routes**

```bash
php artisan route:clear
```

---

## 📦 **COMPOSER**

### **Installer les dépendances**

```bash
composer install
```

### **Mettre à jour les packages**

```bash
composer update
```

### **Regénérer l'autoload**

```bash
composer dump-autoload
```

---

## 🧹 **NETTOYAGE**

### **Supprimer les anciennes données analytics**

```bash
php artisan tinker

# Supprimer les données > 12 mois
$cutoffDate = now()->subMonths(12);

\DB::table('user_analytics')->where('first_visit', '<', $cutoffDate)->delete();
\DB::table('page_views')->where('visited_at', '<', $cutoffDate)->delete();
\DB::table('user_sessions')->where('started_at', '<', $cutoffDate)->delete();

echo "✅ Nettoyage terminé";
```

### **Supprimer toutes les données analytics**

```bash
php artisan tinker

\DB::table('user_analytics')->truncate();
\DB::table('page_views')->truncate();
\DB::table('user_sessions')->truncate();

echo "✅ Toutes les données analytics supprimées";
```

---

## 🔍 **VÉRIFICATIONS**

### **Vérifier que les tables existent**

```bash
php artisan tinker

Schema::hasTable('user_analytics');    // true
Schema::hasTable('page_views');        // true
Schema::hasTable('user_sessions');     // true
Schema::hasTable('users');             // true
Schema::hasTable('admins');            // true
```

### **Vérifier les colonnes**

```bash
php artisan tinker

# Colonnes de user_analytics
Schema::getColumnListing('user_analytics');

# Colonnes de users (vérifier session_token)
Schema::hasColumn('users', 'session_token'); // true
Schema::hasColumn('admins', 'session_token'); // true
```

### **Vérifier les fichiers GeoIP**

```bash
php artisan tinker

$path = storage_path('app/geoip/GeoLite2-City.mmdb');
echo "Fichier existe : " . (file_exists($path) ? '✅ OUI' : '❌ NON');

if (file_exists($path)) {
    echo "\nTaille : " . round(filesize($path) / 1024 / 1024, 2) . " MB";
    echo "\nDate : " . date('Y-m-d H:i:s', filemtime($path));
}
```

---

## 📊 **STATISTIQUES**

### **Statistiques analytics**

```bash
php artisan tinker

// Visiteurs aujourd'hui
\Modules\LMS\Models\Analytics\UserAnalytics::whereDate('first_visit', today())->count();

// Pages vues aujourd'hui
\Modules\LMS\Models\Analytics\PageView::whereDate('visited_at', today())->count();

// Sessions actives
\Modules\LMS\Models\Analytics\UserSession::whereNull('ended_at')->count();

// Top 5 pays
\Modules\LMS\Models\Analytics\UserAnalytics::select('country', \DB::raw('count(*) as count'))
    ->groupBy('country')
    ->orderByDesc('count')
    ->limit(5)
    ->get();
```

---

## 🚀 **DÉVELOPPEMENT**

### **Générer un contrôleur**

```bash
php artisan make:controller NomController
```

### **Générer un model**

```bash
php artisan make:model NomModel -m
# -m : Créer aussi la migration
```

### **Générer une migration**

```bash
php artisan make:migration nom_de_la_migration
```

### **Générer une commande**

```bash
php artisan make:command NomCommande
```

---

## 🔄 **SERVEUR DE DÉVELOPPEMENT**

### **Démarrer le serveur**

```bash
php artisan serve
```

**URL** : http://127.0.0.1:8000

### **Démarrer sur un port spécifique**

```bash
php artisan serve --port=8080
```

---

## 📧 **QUEUE / JOBS**

### **Traiter la queue**

```bash
# Une fois
php artisan queue:work --once

# En continu
php artisan queue:work

# Spécifier une connexion
php artisan queue:work redis
```

### **Voir les jobs en attente**

```bash
php artisan queue:monitor
```

---

## 🎯 **COMMANDES PERSONNALISÉES DU PROJET**

### **Analytics**

```bash
# Mettre à jour GeoIP
php artisan geoip:update
```

---

## 📂 **FICHIERS**

### **Logs**

```bash
# Laravel general
storage/logs/laravel.log

# Scheduler (si configuré)
storage/logs/scheduler.log
```

### **Chemins importants**

```bash
# Base GeoIP
storage/app/geoip/GeoLite2-City.mmdb

# Fichiers temporaires
storage/app/temp/

# Cache
bootstrap/cache/
storage/framework/cache/
```

---

## 🎊 **AIDE-MÉMOIRE RAPIDE**

```bash
# Je viens de faire des changements → Nettoyer
php artisan optimize:clear

# Je veux voir les routes → Lister
php artisan route:list

# Je veux tester du code PHP → Console
php artisan tinker

# Je veux voir les tâches planifiées → Lister
php artisan schedule:list

# Je veux mettre à jour GeoIP → Update
php artisan geoip:update

# Je veux voir les statistiques → Dashboard
/admin/analytics (navigateur)
```

---

## 📖 **DOCUMENTATION**

Tous les guides sont dans la racine du projet :

```
GUIDE_FINAL_TOUT_EN_UN.md         ← Commencez ici !
GUIDE_DEMARRAGE_ANALYTICS.md      ← Démarrage rapide
GUIDE_ANALYTICS_COMPLET.md        ← Documentation complète
GUIDE_MAXMIND_INSTALLATION.md     ← Installation MaxMind
GUIDE_COMMANDE_GEOIP.md           ← Commande geoip:update
GUIDE_ACTIVATION_SCHEDULER.md     ← Activer le scheduler
GUIDE_SESSION_UNIQUE.md           ← Session unique
GUIDE_SESSION_MONITOR.md          ← Surveillance session
RESUME_FINAL_IMPLEMENTATION.md    ← Résumé complet
COMMANDES_UTILES.md               ← Ce fichier
```

---

**🎯 Gardez ce fichier à portée de main ! 🎯**

