# 📍 GUIDE - INSTALLATION MAXMIND GEOLITE2

## ✅ INSTALLATION RAPIDE (5 MINUTES)

---

## **ÉTAPE 1 : CRÉER UN COMPTE MAXMIND (GRATUIT)**

1. Allez sur : **https://www.maxmind.com/en/geolite2/signup**
2. Remplissez le formulaire :
   - First Name
   - Last Name
   - Email
   - Password
3. ✅ Cochez "I'm not a robot"
4. Cliquez sur **"Continue"**
5. ✅ Vérifiez votre email et confirmez

---

## **ÉTAPE 2 : GÉNÉRER UNE LICENSE KEY**

1. Connectez-vous sur : **https://www.maxmind.com/en/accounts/current/license-key**
2. Cliquez sur **"Generate new license key"**
3. Nommez-la : `MyLMS Analytics`
4. ✅ **Important** : Cochez **"No"** pour GeoIP Update Program
5. Cliquez sur **"Confirm"**
6. ⚠️ **COPIEZ** la License Key (elle ne sera affichée qu'une seule fois)

```
Exemple de License Key:
abc123DEF456ghi789JKL012mno345PQR
```

---

## **ÉTAPE 3 : TÉLÉCHARGER LA BASE DE DONNÉES**

### **Option A : Téléchargement Manuel (Rapide)**

1. Allez sur : **https://www.maxmind.com/en/accounts/current/geoip/downloads**
2. Trouvez **"GeoLite2 City"**
3. Cliquez sur **"Download GZIP"**
4. Décompressez le fichier `.tar.gz`
5. Vous obtenez : **`GeoLite2-City.mmdb`** (environ 70 MB)

### **Option B : Via Composer (Automatique)**

```bash
composer require geoip2/geoip2:~2.0
```

---

## **ÉTAPE 4 : PLACER LE FICHIER DANS VOTRE PROJET**

1. Créez le dossier :

```bash
mkdir storage/app/geoip
```

2. Copiez **`GeoLite2-City.mmdb`** dans :

```
C:\Users\madou\OneDrive\Desktop\ProjetLaravel\myluc\storage\app\geoip\GeoLite2-City.mmdb
```

---

## **ÉTAPE 5 : VÉRIFIER L'INSTALLATION**

Testez avec Tinker :

```bash
php artisan tinker
```

```php
$reader = new \GeoIp2\Database\Reader(storage_path('app/geoip/GeoLite2-City.mmdb'));
$record = $reader->city('8.8.8.8');
echo $record->country->name; // "United States"
echo $record->city->name; // "Mountain View"
```

Si ça fonctionne → ✅ **Installation réussie !**

---

## **ÉTAPE 6 : MISE À JOUR AUTOMATIQUE (OPTIONNEL)**

MaxMind met à jour la base **toutes les premières semaines du mois**.

### **Créer un script de mise à jour automatique :**

```bash
php artisan make:command UpdateGeoIpDatabase
```

```php
// app/Console/Commands/UpdateGeoIpDatabase.php

public function handle()
{
    $licenseKey = env('MAXMIND_LICENSE_KEY');
    $url = "https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&license_key={$licenseKey}&suffix=tar.gz";
    
    $filePath = storage_path('app/geoip/GeoLite2-City.tar.gz');
    
    // Télécharger
    file_put_contents($filePath, file_get_contents($url));
    
    // Décompresser et remplacer
    // ... (code de décompression)
    
    $this->info('GeoIP database updated successfully!');
}
```

**Programmer dans le Kernel :**

```php
// app/Console/Kernel.php

protected function schedule(Schedule $schedule)
{
    $schedule->command('geoip:update')->monthlyOn(1, '03:00');
}
```

---

## **CONFIGURATION `.env`**

Ajoutez votre License Key :

```env
MAXMIND_LICENSE_KEY=abc123DEF456ghi789JKL012mno345PQR
```

---

## **ALTERNATIVE : UTILISER ip-api.com (SANS INSTALLATION)**

Si vous voulez **tester sans MaxMind** d'abord :

**Le service Analytics fonctionne automatiquement avec ip-api.com** (45 req/min gratuit) si MaxMind n'est pas disponible.

**Aucune configuration nécessaire !**

---

## ✅ **STATUT ACTUEL**

```
┌──────────────────────────────────────────────────┐
│ ANALYTICS SYSTÈME - PRESQUE PRÊT !               │
│                                                  │
│ ✅ Base de données : OK                          │
│ ✅ Models : OK                                   │
│ ✅ Service : OK (avec fallback ip-api.com)       │
│ ✅ Contrôleur : OK                               │
│ ✅ Routes API : OK                               │
│ ✅ JavaScript Tracker : OK                       │
│ ✅ Champs démographiques : OK (déjà existants)   │
│                                                  │
│ ⏳ MaxMind : À configurer (optionnel)            │
│ ⏳ Dashboard Admin : À créer                     │
│                                                  │
│ 🎯 FONCTIONNEL MAINTENANT avec ip-api.com !     │
└──────────────────────────────────────────────────┘
```

---

## 🚀 **VOUS POUVEZ DÉJÀ TESTER !**

Même sans MaxMind, le système fonctionne avec **ip-api.com** (fallback automatique).

**Voulez-vous :**
1. ✅ **Tester maintenant** (sans MaxMind)
2. ✅ **Installer MaxMind** d'abord
3. ✅ **Créer le dashboard admin** avant de tester

**Que préférez-vous ? 🎯**

