# ⏰ GUIDE - ACTIVATION DU SCHEDULER LARAVEL

## 🎯 TÂCHES PLANIFIÉES CONFIGURÉES

Le scheduler Laravel a été configuré avec **3 tâches automatiques** :

```
┌──────────────────────────────────────────────────────┐
│ TÂCHES PLANIFIÉES                                    │
├──────────────────────────────────────────────────────┤
│ 1. Queue Worker        → Toutes les minutes         │
│ 2. GeoIP Update        → 1er de chaque mois (3h)    │
│ 3. Cleanup Analytics   → 1er de chaque mois (4h)    │
└──────────────────────────────────────────────────────┘
```

---

## 📋 **DÉTAILS DES TÂCHES**

### **1️⃣ Queue Worker**
- **Quand** : Toutes les minutes
- **Commande** : `queue:work --sleep=3 --tries=3`
- **But** : Traiter les jobs en arrière-plan (emails, notifications, etc.)

### **2️⃣ GeoIP Update**
- **Quand** : 1er de chaque mois à 3h du matin
- **Commande** : `geoip:update`
- **But** : Télécharger et installer la dernière base MaxMind
- **Logs** : `storage/logs/laravel.log` → `[Scheduler] Base GeoIP...`

### **3️⃣ Cleanup Analytics (RGPD)**
- **Quand** : 1er de chaque mois à 4h du matin
- **Action** : Supprime les données analytics > 12 mois
- **But** : Conformité RGPD (conservation limitée)
- **Logs** : `storage/logs/laravel.log` → `[Scheduler] Nettoyage analytics...`

---

## 🚀 **ACTIVER LE SCHEDULER**

Le scheduler Laravel **NE FONCTIONNE PAS** automatiquement. Vous devez l'activer avec :

---

### **MÉTHODE 1 : WINDOWS TASK SCHEDULER** (Recommandé pour Windows)

#### **Étape 1 : Créer le script batch**

Créez un fichier `run-scheduler.bat` dans votre projet :

```batch
@echo off
cd C:\Users\madou\OneDrive\Desktop\ProjetLaravel\myluc
php artisan schedule:run >> storage\logs\scheduler.log 2>&1
```

#### **Étape 2 : Ouvrir Task Scheduler**

```
1. Appuyez sur Windows + R
2. Tapez : taskschd.msc
3. Cliquez sur "OK"
```

#### **Étape 3 : Créer une nouvelle tâche**

```
1. Clic droit sur "Bibliothèque du planificateur de tâches"
2. "Créer une tâche de base..."
3. Nom : "Laravel Scheduler - MyLMS"
4. Déclencheur : "À intervalle régulier"
5. Intervalle : "Chaque jour"
6. Action : "Démarrer un programme"
7. Programme/script : C:\Users\madou\OneDrive\Desktop\ProjetLaravel\myluc\run-scheduler.bat
8. Cliquez sur "Terminer"
```

#### **Étape 4 : Configurer l'intervalle (toutes les minutes)**

```
1. Clic droit sur la tâche créée → "Propriétés"
2. Onglet "Déclencheurs" → Modifier
3. Cochez "Répéter la tâche toutes les :" → 1 minute
4. Pendant : "Indéfiniment"
5. OK
```

#### **Étape 5 : Tester**

```
1. Clic droit sur la tâche → "Exécuter"
2. Vérifiez : storage/logs/scheduler.log
```

---

### **MÉTHODE 2 : LARAGON** (Si vous utilisez Laragon)

Laragon a un scheduler intégré !

```
1. Ouvrez Laragon
2. Menu → Tools → Quick app → "Laravel Scheduler"
3. Votre projet → Sélectionnez "MyLMS"
4. ✅ Activé automatiquement !
```

---

### **MÉTHODE 3 : SCRIPT POWERSHELL** (Alternative)

Créez `run-scheduler.ps1` :

```powershell
# run-scheduler.ps1
Set-Location "C:\Users\madou\OneDrive\Desktop\ProjetLaravel\myluc"
php artisan schedule:run *>> storage/logs/scheduler.log
```

Ajoutez au **Task Scheduler** :
```
Programme : powershell.exe
Arguments : -File C:\...\run-scheduler.ps1
```

---

### **MÉTHODE 4 : SERVEUR LINUX (Production)**

```bash
# Éditer le crontab
crontab -e

# Ajouter cette ligne
* * * * * cd /path/to/myluc && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🧪 **TESTER LE SCHEDULER**

### **Test 1 : Exécution manuelle**

```bash
php artisan schedule:run
```

**Résultat attendu** :
```
No scheduled commands are ready to run.
```

Ou si une tâche doit s'exécuter :
```
Running scheduled command: 'C:\...\artisan' geoip:update
```

### **Test 2 : Vérifier les tâches planifiées**

```bash
php artisan schedule:list
```

**Résultat attendu** :
```
┌────────────────────────────────────────────────────────┐
│ Command                      Next Due                  │
├────────────────────────────────────────────────────────┤
│ queue:work --sleep=3 ...     2025-10-29 12:51:00       │
│ geoip:update                 2025-11-01 03:00:00       │
│ cleanup-old-analytics        2025-11-01 04:00:00       │
└────────────────────────────────────────────────────────┘
```

### **Test 3 : Forcer l'exécution d'une commande**

```bash
# Tester la mise à jour GeoIP manuellement
php artisan geoip:update

# Tester le nettoyage analytics manuellement
php artisan tinker
```

```php
$cutoffDate = now()->subMonths(12);
$deletedAnalytics = \DB::table('user_analytics')->where('first_visit', '<', $cutoffDate)->delete();
echo "Supprimé : " . $deletedAnalytics . " entrées";
```

---

## 📊 **VÉRIFIER QUE LE SCHEDULER FONCTIONNE**

### **Logs à surveiller** :

**Fichier** : `storage/logs/scheduler.log` (si configuré)

**Contenu attendu chaque minute** :
```
Running scheduled command: ...
```

**Fichier** : `storage/logs/laravel.log`

**Contenu attendu le 1er du mois** :
```
[2025-11-01 03:00:00] local.INFO: ✅ [Scheduler] Base GeoIP mise à jour avec succès
[2025-11-01 04:00:00] local.INFO: 🧹 [Scheduler] Nettoyage analytics terminé {
    "user_analytics_deleted": 245,
    "page_views_deleted": 1234,
    "user_sessions_deleted": 189
}
```

---

## 🔧 **DÉPANNAGE**

### **❌ Le scheduler ne s'exécute pas**

**Vérifiez** :

1. **Task Scheduler est actif ?**
```
Task Scheduler → Bibliothèque → "Laravel Scheduler - MyLMS"
→ État : "Prêt" ou "En cours d'exécution"
```

2. **Le script batch fonctionne ?**
```
Double-cliquez sur run-scheduler.bat
→ Vérifiez storage/logs/scheduler.log
```

3. **Les permissions sont OK ?**
```
Le dossier storage/logs/ doit être accessible en écriture
```

---

### **❌ Erreur : "schedule:run not found"**

**Solution** :
```bash
composer dump-autoload
php artisan optimize:clear
```

---

## ⚙️ **CONFIGURATION AVANCÉE**

### **Changer l'heure de mise à jour GeoIP** :

**Fichier** : `bootstrap/app.php` (ligne 31)

```php
// De 3h du matin à 2h du matin
->monthlyOn(1, '02:00')

// De mensuel à hebdomadaire (chaque lundi à 3h)
->weeklyOn(1, '03:00')

// De mensuel à quotidien (chaque jour à 3h)
->dailyAt('03:00')
```

### **Changer la durée de conservation analytics** :

**Fichier** : `bootstrap/app.php` (ligne 43)

```php
// De 12 mois à 6 mois
$cutoffDate = now()->subMonths(6);

// De 12 mois à 24 mois
$cutoffDate = now()->subMonths(24);

// De 12 mois à 90 jours
$cutoffDate = now()->subDays(90);
```

---

## 📅 **CALENDRIER DES TÂCHES**

| Tâche | Fréquence | Heure | Jour |
|-------|-----------|-------|------|
| **Queue Worker** | Toutes les minutes | -- | Tous les jours |
| **GeoIP Update** | Mensuelle | 3h | 1er du mois |
| **Cleanup Analytics** | Mensuelle | 4h | 1er du mois |

---

## 🎯 **NOTIFICATIONS PAR EMAIL (OPTIONNEL)**

### **Recevoir un email après chaque tâche** :

```php
// bootstrap/app.php

$schedule->command('geoip:update')
    ->monthlyOn(1, '03:00')
    ->emailOutputOnFailure('admin@mylms.com');
```

**Prérequis** : Configurez l'envoi d'emails dans `.env` :

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre_email@gmail.com
MAIL_PASSWORD=votre_mot_de_passe
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=votre_email@gmail.com
MAIL_FROM_NAME="MyLMS"
```

---

## ✅ **VÉRIFICATION FINALE**

### **Checklist** :

- [ ] 1. Fichier `run-scheduler.bat` créé
- [ ] 2. Tâche Windows Task Scheduler créée
- [ ] 3. Intervalle configuré (toutes les minutes)
- [ ] 4. Test manuel : `php artisan schedule:run` ✅
- [ ] 5. Test liste : `php artisan schedule:list` ✅
- [ ] 6. Logs présents : `storage/logs/scheduler.log`

---

## 🎊 **RÉSUMÉ**

```
┌────────────────────────────────────────────────────┐
│ ⏰ SCHEDULER LARAVEL CONFIGURÉ !                   │
│                                                    │
│ ✓ 3 tâches planifiées                             │
│ ✓ Queue Worker : Chaque minute                    │
│ ✓ GeoIP Update : Mensuel (1er à 3h)               │
│ ✓ Cleanup Analytics : Mensuel (1er à 4h)          │
│                                                    │
│ ⏳ Activation requise :                            │
│ → Windows Task Scheduler                           │
│ → OU Laragon (si utilisé)                          │
│ → OU Cron (Linux production)                       │
│                                                    │
│ 🎯 Prêt à automatiser ! 🎯                        │
└────────────────────────────────────────────────────┘
```

---

## 🚀 **PROCHAINES ÉTAPES**

1. ✅ **Créez** le fichier `run-scheduler.bat`
2. ✅ **Configurez** Windows Task Scheduler
3. ✅ **Testez** avec `php artisan schedule:run`
4. ✅ **Vérifiez** les logs le 1er du mois prochain

---

**Le scheduler automatisera tout pour vous ! 🎉⏰🚀**

