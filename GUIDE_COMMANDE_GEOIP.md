# 🌍 GUIDE - COMMANDE GEOIP:UPDATE

## ✅ TÉLÉCHARGEMENT ET INSTALLATION AUTOMATIQUE DE MAXMIND

---

## 🎯 **COMMANDE CRÉÉE**

```bash
php artisan geoip:update
```

**Description** : Télécharge et installe automatiquement la base de données MaxMind GeoLite2-City

---

## 📋 **PRÉREQUIS**

### **1️⃣ Créer un compte MaxMind (GRATUIT)**

1. Allez sur : https://www.maxmind.com/en/geolite2/signup
2. Créez un compte gratuit
3. Vérifiez votre email

### **2️⃣ Générer une License Key**

1. Connectez-vous : https://www.maxmind.com/en/accounts/current/license-key
2. Cliquez sur **"Generate new license key"**
3. Nommez-la : `MyLMS Analytics`
4. ⚠️ Cochez **"No"** pour GeoIP Update Program
5. **COPIEZ** la clé (elle ne sera affichée qu'une fois)

Exemple :
```
abc123DEF456ghi789JKL012mno345PQR
```

### **3️⃣ Ajouter la clé dans .env**

Ouvrez votre fichier `.env` et ajoutez :

```env
MAXMIND_LICENSE_KEY=abc123DEF456ghi789JKL012mno345PQR
```

---

## 🚀 **UTILISATION**

### **Commande simple :**

```bash
php artisan geoip:update
```

### **Ce qui se passe :**

```
🌍 Mise à jour de la base de données GeoIP...
📥 Téléchargement de GeoLite2-City...
✅ Téléchargement terminé ! Taille : 68.45 MB
📦 Décompression...
✅ Décompression réussie avec tar
💾 Ancienne base sauvegardée
✅ Base de données installée : C:\...\storage\app\geoip\GeoLite2-City.mmdb
🧹 Fichiers temporaires supprimés
✅ Vérification OK - Taille : 68.45 MB
🎉 Mise à jour terminée avec succès !
```

---

## ⏱️ **AUTOMATISER LA MISE À JOUR MENSUELLE**

MaxMind met à jour la base **toutes les premières mardis du mois**.

### **Ajouter au Kernel** :

Éditez `bootstrap/app.php` ou créez un `Kernel.php` :

```php
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    // ...
    ->withSchedule(function (Schedule $schedule) {
        // Mise à jour GeoIP le 1er de chaque mois à 3h du matin
        $schedule->command('geoip:update')->monthlyOn(1, '03:00');
    })
    // ...
```

### **Activer le scheduler** :

Ajoutez au cron (Linux) ou Task Scheduler (Windows) :

**Windows Task Scheduler** :
```
Program: C:\path\to\php.exe
Arguments: C:\path\to\artisan schedule:run
Trigger: Every minute
```

**Linux Cron** :
```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🔧 **DÉPANNAGE**

### **❌ Erreur : "MAXMIND_LICENSE_KEY non définie"**

**Solution** :
```bash
# 1. Vérifiez que la clé est dans .env
cat .env | grep MAXMIND

# 2. Si absente, ajoutez-la
echo "MAXMIND_LICENSE_KEY=votre_clé" >> .env

# 3. Nettoyez le cache
php artisan config:clear
```

---

### **❌ Erreur : "tar n'est pas disponible"**

**Solution A : Installer tar sur Windows**

Windows 10 (build 17063+) et Windows 11 ont `tar` intégré.

Vérifiez :
```powershell
tar --version
```

Si absent, **installez 7-Zip** et décompressez manuellement.

**Solution B : Installation manuelle**

```bash
# 1. Exécutez la commande (elle téléchargera le fichier)
php artisan geoip:update

# 2. Suivez les instructions affichées :
# - Le fichier sera dans storage/app/temp/GeoLite2-City.tar.gz
# - Décompressez avec 7-Zip
# - Copiez GeoLite2-City.mmdb vers storage/app/geoip/
```

---

### **❌ Erreur : "Erreur de téléchargement : 401"**

**Cause** : License Key invalide ou expirée

**Solution** :
```bash
# 1. Vérifiez votre clé sur MaxMind
# 2. Générez une nouvelle clé si nécessaire
# 3. Mettez à jour .env
# 4. Nettoyez le cache
php artisan config:clear
```

---

## 🧪 **VÉRIFIER L'INSTALLATION**

### **Test 1 : Vérifier que le fichier existe**

```bash
php artisan tinker
```

```php
$path = storage_path('app/geoip/GeoLite2-City.mmdb');
echo "Fichier existe : " . (file_exists($path) ? '✅ OUI' : '❌ NON');

if (file_exists($path)) {
    echo "\nTaille : " . round(filesize($path) / 1024 / 1024, 2) . " MB";
}
```

### **Test 2 : Tester la géolocalisation**

```php
$reader = new \GeoIp2\Database\Reader(storage_path('app/geoip/GeoLite2-City.mmdb'));

// Test avec l'IP de Google
$record = $reader->city('8.8.8.8');

echo "IP : 8.8.8.8\n";
echo "Pays : " . $record->country->name . "\n";
echo "Ville : " . $record->city->name . "\n";
echo "Timezone : " . $record->location->timeZone . "\n";

// Résultat attendu :
// Pays : United States
// Ville : Mountain View
// Timezone : America/Los_Angeles
```

### **Test 3 : Tester avec le système analytics**

```php
use Modules\LMS\Services\AnalyticsService;

$service = new AnalyticsService();

$data = [
    'session_id' => 'test_maxmind',
    'user_id' => null,
    'ip_address' => '1.1.1.1', // IP Cloudflare (Australie)
    'user_agent' => 'Mozilla/5.0...',
    'referrer' => null,
    'page_url' => 'http://test.com',
];

$service->track($data);

// Vérifier le résultat
$result = \Modules\LMS\Models\Analytics\UserAnalytics::where('session_id', 'test_maxmind')->first();

echo "Pays détecté : " . $result->country . "\n";
echo "Ville détectée : " . $result->city . "\n";

// Résultat attendu :
// Pays détecté : Australia
// Ville détectée : Sydney (ou autre selon MaxMind)
```

---

## 📊 **DIFFÉRENCE AVANT/APRÈS MAXMIND**

| Aspect | AVANT (ip-api.com) | APRÈS (MaxMind) |
|--------|-------------------|-----------------|
| **Vitesse** | 200-500ms | < 1ms ⚡ |
| **Limite** | 45 req/min | Illimité ♾️ |
| **Fiabilité** | Dépend du réseau | 100% (offline) |
| **Précision** | ~95% | ~95% (similaire) |
| **Coût** | Gratuit | Gratuit (GeoLite2) |
| **RGPD** | Données partagées | Données en local ✅ |

---

## 🔄 **MISE À JOUR MANUELLE**

Si la commande automatique échoue :

### **Méthode 1 : Via le site MaxMind**

```
1. https://www.maxmind.com/en/accounts/current/geoip/downloads
2. Download "GeoLite2 City" (GZIP format)
3. Décompressez avec 7-Zip (2 fois : .gz puis .tar)
4. Copiez GeoLite2-City.mmdb vers :
   C:\Users\madou\OneDrive\Desktop\ProjetLaravel\myluc\storage\app\geoip\
```

### **Méthode 2 : Via PowerShell**

```powershell
# Télécharger
$licenseKey = "VOTRE_CLE"
$url = "https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&license_key=$licenseKey&suffix=tar.gz"
Invoke-WebRequest -Uri $url -OutFile "$env:TEMP\GeoLite2-City.tar.gz"

# Décompresser (avec tar Windows)
cd $env:TEMP
tar -xzf GeoLite2-City.tar.gz

# Copier
copy GeoLite2-City_*\GeoLite2-City.mmdb C:\Users\madou\OneDrive\Desktop\ProjetLaravel\myluc\storage\app\geoip\
```

---

## 📅 **CALENDRIER DE MISE À JOUR**

MaxMind publie une nouvelle version :
- 🗓️ **Tous les premiers mardis du mois**
- 📦 Taille : ~70 MB
- ⏱️ Temps de mise à jour : 2-5 minutes

**Recommandation** : Configurez le scheduler pour automatiser !

---

## ✅ **CHECKLIST COMPLÈTE**

- [ ] 1. Créer un compte MaxMind (gratuit)
- [ ] 2. Générer une License Key
- [ ] 3. Ajouter `MAXMIND_LICENSE_KEY` dans `.env`
- [ ] 4. Exécuter `php artisan geoip:update`
- [ ] 5. Vérifier avec `php artisan tinker`
- [ ] 6. (Optionnel) Configurer le scheduler mensuel

---

## 🎯 **UTILISATION QUOTIDIENNE**

### **Mise à jour manuelle (quand vous voulez) :**

```bash
php artisan geoip:update
```

### **Vérifier la date de la base actuelle :**

```bash
php artisan tinker
```

```php
$path = storage_path('app/geoip/GeoLite2-City.mmdb');
if (file_exists($path)) {
    $date = date('Y-m-d H:i:s', filemtime($path));
    echo "Dernière mise à jour : " . $date;
}
```

---

## 📞 **SUPPORT**

**Si la commande échoue** :
1. Vérifiez `MAXMIND_LICENSE_KEY` dans `.env`
2. Vérifiez que vous avez internet
3. Suivez les instructions manuelles affichées
4. Consultez `GUIDE_MAXMIND_INSTALLATION.md`

---

## 🎊 **CONCLUSION**

```
┌────────────────────────────────────────────────────┐
│ ✅ COMMANDE GEOIP:UPDATE CRÉÉE !                   │
│                                                    │
│ Usage : php artisan geoip:update                   │
│                                                    │
│ ✓ Télécharge automatiquement                      │
│ ✓ Décompresse automatiquement                     │
│ ✓ Installe automatiquement                        │
│ ✓ Sauvegarde l'ancienne version                   │
│                                                    │
│ 🚀 Mise à jour en 1 seule commande ! 🚀           │
└────────────────────────────────────────────────────┘
```

---

**Essayez maintenant : `php artisan geoip:update` ! 🎯**

