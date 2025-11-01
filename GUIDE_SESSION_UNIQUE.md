# 🔐 GUIDE - SYSTÈME DE SESSION UNIQUE

## ✅ IMPLÉMENTATION TERMINÉE !

Le système de **Session Unique** est maintenant actif sur votre plateforme LMS.

---

## 📋 CE QUI A ÉTÉ FAIT

### **1️⃣ BASE DE DONNÉES**

✅ **Colonne `session_token` ajoutée à :**
- Table `users` (Student, Instructor, Organization)
- Table `admins` (Administrateurs)

```sql
ALTER TABLE users ADD session_token VARCHAR(100) NULL;
ALTER TABLE admins ADD session_token VARCHAR(100) NULL;
```

---

### **2️⃣ MODELS MODIFIÉS**

✅ **Modules/LMS/app/Models/User.php**
```php
protected $fillable = [..., 'session_token'];
```

✅ **Modules/LMS/app/Models/Auth/Admin.php**
```php
protected $fillable = [..., 'session_token'];
```

---

### **3️⃣ GÉNÉRATION DU TOKEN À LA CONNEXION**

✅ **UserRepository::login()** (Student, Instructor, Organization)
- Génère un token unique à chaque connexion
- Sauvegarde dans la BDD : `$user->session_token`
- Sauvegarde en session : `session['session_token_web']`

✅ **AdminController::login()** (Admin)
- Génère un token unique à chaque connexion
- Sauvegarde dans la BDD : `$admin->session_token`
- Sauvegarde en session : `session['session_token_admin']`

---

### **4️⃣ MIDDLEWARE DE VÉRIFICATION**

✅ **Modules/LMS/app/Http/Middleware/CheckSessionToken.php**

**Ce middleware s'exécute à chaque requête et :**
1. Vérifie si l'utilisateur est authentifié
2. Compare le token en session avec celui en BDD
3. Si différent → **Déconnexion automatique**

---

### **5️⃣ ROUTES PROTÉGÉES**

✅ **Le middleware `check.session.token` est appliqué sur :**
- ✅ Routes Student (`/dashboard/*`)
- ✅ Routes Instructor (`/instructor/*`)
- ✅ Routes Organization (`/org/*`)
- ✅ Routes Admin (`/admin/*`)

---

## 🎯 COMMENT ÇA FONCTIONNE ?

### **SCÉNARIO 1 : CONNEXION SUR PC**

```
1. Utilisateur se connecte sur PC
   ↓
2. Token généré : "abc123..."
   ↓
3. Sauvegardé en BDD : users.session_token = "abc123..."
   ↓
4. Sauvegardé en session : session['session_token_web'] = "abc123..."
   ↓
5. Utilisateur navigue normalement
```

---

### **SCÉNARIO 2 : CONNEXION SUR TÉLÉPHONE**

```
1. MÊME utilisateur se connecte sur téléphone
   ↓
2. NOUVEAU token généré : "xyz789..."
   ↓
3. ⚠️ ÉCRASEMENT en BDD : users.session_token = "xyz789..."
   ↓
4. Sauvegardé en session téléphone : session['session_token_web'] = "xyz789..."
   ↓
5. L'ancien token du PC ("abc123...") n'est plus valide
```

---

### **SCÉNARIO 3 : L'UTILISATEUR SUR PC FAIT UNE REQUÊTE**

```
1. Middleware CheckSessionToken s'exécute
   ↓
2. Token en session PC : "abc123..."
   ↓
3. Token en BDD : "xyz789..." (du téléphone)
   ↓
4. ❌ PAS ÉGAL !
   ↓
5. Actions :
   - Auth::logout()
   - session()->invalidate()
   - redirect('/login')->with('warning', '⚠️ Déconnecté...')
   ↓
6. Message affiché :
   "⚠️ Vous avez été déconnecté car une nouvelle connexion 
    a été détectée sur un autre appareil."
```

---

## 📊 LOGS GÉNÉRÉS

### **Lors de la connexion :**

```
🔐 [Session Unique] Token généré pour utilisateur
{
    "user_id": 53,
    "email": "student@example.com",
    "guard": "student",
    "token_preview": "aBcDeFgHiJ..."
}
```

### **Lors de la déconnexion automatique :**

```
⚠️ [Session Unique] Déconnexion détectée
{
    "guard": "web",
    "user_id": 53,
    "email": "student@example.com",
    "reason": "Token mismatch - nouvelle connexion détectée ailleurs"
}
```

---

## 🧪 COMMENT TESTER ?

### **TEST 1 : CONNEXION DOUBLE**

1. **Ouvrez 2 navigateurs** (Chrome et Firefox par exemple)
2. **Connectez-vous avec le MÊME compte** dans les 2 navigateurs
3. **Dans le navigateur 1** : Cliquez sur un lien du dashboard
4. **Résultat attendu** : Vous êtes déconnecté automatiquement avec le message :
   ```
   ⚠️ Vous avez été déconnecté car une nouvelle connexion 
      a été détectée sur un autre appareil.
   ```

### **TEST 2 : CONNEXION PC + TÉLÉPHONE**

1. Connectez-vous sur votre **PC**
2. Connectez-vous sur votre **téléphone** avec le même compte
3. Retournez sur le **PC** et rafraîchissez la page
4. **Résultat attendu** : Déconnexion automatique sur le PC

---

## ⚙️ CONFIGURATION

### **Clé de session par guard :**

| Guard | Clé de session | Route de redirection |
|-------|---------------|---------------------|
| `web` (Student, Instructor, Organization) | `session_token_web` | `login` |
| `admin` | `session_token_admin` | `admin.login` |

### **Désactiver le système :**

Si vous voulez **temporairement désactiver** le système :

1. **Commentez le middleware dans les routes :**

```php
// Modules/LMS/routes/student.php (ligne 12)
['prefix' => 'dashboard', 'as' => 'student.', 'middleware' => ['auth', 'role:Student', 'checkInstaller' /*, 'check.session.token' */]],
```

2. **Nettoyez le cache :**

```bash
php artisan config:clear
php artisan route:clear
```

---

## 🔧 PERSONNALISATION

### **Changer le message de déconnexion :**

**Fichier** : `Modules/LMS/app/Http/Middleware/CheckSessionToken.php`

```php
'message' => '⚠️ Vous avez été déconnecté car une nouvelle connexion a été détectée sur un autre appareil.'
```

### **Permettre 2 sessions simultanées :**

Si vous voulez autoriser **2 appareils maximum**, modifiez le middleware pour stocker un **tableau de tokens** au lieu d'un seul token.

---

## 📂 FICHIERS MODIFIÉS

### **Créés :**
1. `database/migrations/2025_10_29_114015_add_session_token_to_users_and_admins_tables.php`
2. `Modules/LMS/app/Http/Middleware/CheckSessionToken.php`

### **Modifiés :**
1. `Modules/LMS/app/Models/User.php`
2. `Modules/LMS/app/Models/Auth/Admin.php`
3. `Modules/LMS/app/Repositories/Auth/UserRepository.php`
4. `Modules/Roles/app/Http/Controllers/AdminController.php`
5. `bootstrap/app.php`
6. `Modules/LMS/routes/student.php`
7. `Modules/LMS/routes/instructor.php`
8. `Modules/LMS/routes/organization.php`
9. `Modules/LMS/routes/admin.php`
10. `Modules/Roles/routes/web.php`

---

## ✅ STATUT

```
┌────────────────────────────────────────────┐
│ ✅ SYSTÈME DE SESSION UNIQUE ACTIF         │
│                                            │
│ ✓ Base de données : OK                    │
│ ✓ Models : OK                             │
│ ✓ Connexion : OK                          │
│ ✓ Middleware : OK                         │
│ ✓ Routes : OK                             │
│                                            │
│ 🎉 PRÊT À UTILISER !                      │
└────────────────────────────────────────────┘
```

---

## 🚀 PROCHAINES ÉTAPES

1. ✅ **Testez** avec 2 navigateurs
2. ✅ **Vérifiez les logs** dans `storage/logs/laravel.log`
3. ✅ **Testez** sur PC + Mobile
4. ✅ **Personnalisez** le message si nécessaire

---

## ❓ QUESTIONS FRÉQUENTES

### **Q1 : Et si l'utilisateur veut rester connecté sur 2 appareils ?**
**R** : Pour l'instant, c'est **1 seul appareil**. Si vous voulez permettre plusieurs appareils, il faudrait stocker un tableau de tokens (modification plus complexe).

### **Q2 : Le "Se souvenir de moi" fonctionne encore ?**
**R** : Oui ! Le système `remember_me` existant n'est **PAS affecté**. Ce sont 2 systèmes indépendants.

### **Q3 : Que se passe-t-il si je supprime `session_token` de la BDD ?**
**R** : L'utilisateur sera déconnecté au prochain rechargement de page et devra se reconnecter.

---

## 📞 SUPPORT

Si vous rencontrez un problème :
1. Vérifiez les logs : `storage/logs/laravel.log`
2. Recherchez : `[Session Unique]`
3. Vérifiez que les colonnes existent en BDD
4. Nettoyez les caches : `php artisan config:clear`

---

**🎉 Félicitations ! Le système de session unique est maintenant opérationnel ! 🎉**

