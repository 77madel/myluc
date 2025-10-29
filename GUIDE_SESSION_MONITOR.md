# 🔔 GUIDE - SURVEILLANCE AUTOMATIQUE DE SESSION

## ✅ AMÉLIORATION DU SYSTÈME DE SESSION UNIQUE

Le système a été amélioré avec une **surveillance automatique** qui détecte immédiatement les nouvelles connexions !

---

## 🎯 CE QUI A ÉTÉ AJOUTÉ

### **1️⃣ VÉRIFICATION AUTOMATIQUE EN ARRIÈRE-PLAN**

✅ **Composant JavaScript** : `session-monitor.blade.php`
- ⏰ Vérifie la validité de la session **toutes les 30 secondes**
- 👁️ Vérification **immédiate** quand l'utilisateur revient sur l'onglet
- 🔄 Vérification **automatique** au changement de visibilité de la page

✅ **API de vérification** : `SessionCheckController`
- Endpoint : `POST /session/check`
- Compare le token en session avec celui en BDD
- Retourne `valid` ou `invalid`

---

## 🚀 COMMENT ÇA FONCTIONNE MAINTENANT ?

### **SCÉNARIO : CONNEXION SUR 2 APPAREILS**

```
┌─────────────────────────────────────────────────────┐
│ APPAREIL 1 (PC) - Utilisateur connecté             │
│ ↓                                                   │
│ • Vérification toutes les 30 secondes               │
│ • Script JavaScript actif en arrière-plan           │
└─────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│ APPAREIL 2 (Téléphone) - Nouvelle connexion        │
│ ↓                                                   │
│ 1. L'utilisateur se connecte                        │
│ 2. Token mis à jour en BDD                          │
│ 3. Ancien token du PC devient invalide              │
└─────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│ APPAREIL 1 (PC) - DÉTECTION AUTOMATIQUE            │
│ ↓                                                   │
│ • Après 0-30 secondes (dépend du timing)            │
│ • Script vérifie : POST /session/check              │
│ • Réponse : { "status": "invalid" }                 │
│ ↓                                                   │
│ ACTION IMMÉDIATE:                                   │
│ 1. ⚠️ Toastr warning affiché                       │
│    "Vous avez été déconnecté..."                    │
│ 2. ⏱️ Redirection après 2 secondes                 │
│ 3. 🚪 Déconnexion automatique                      │
└─────────────────────────────────────────────────────┘
```

---

## ⏱️ TEMPS DE DÉTECTION

### **Délai de détection :**

| Scénario | Temps de détection |
|----------|-------------------|
| **Utilisateur actif** (navigue) | 0-30 secondes max |
| **Utilisateur inactif** (page ouverte) | 30 secondes max |
| **Utilisateur revient sur l'onglet** | Immédiat (< 1s) |
| **Utilisateur change d'onglet** | À la prochaine vérification |

---

## 🎨 INTERFACE UTILISATEUR

### **Message affiché :**

```
┌──────────────────────────────────────────────────┐
│ ⚠️ AVERTISSEMENT                                 │
│                                                  │
│ Vous avez été déconnecté car une nouvelle       │
│ connexion a été détectée sur un autre appareil.  │
│                                                  │
│ Redirection dans 2 secondes...                   │
└──────────────────────────────────────────────────┘
```

**Type** : Toastr Warning
- ⏱️ Durée : 5 secondes
- ✖️ Bouton fermer : Oui
- 📊 Barre de progression : Oui
- 🔄 Redirection automatique : Après 2 secondes

---

## 📊 LOGS DE SURVEILLANCE

### **Dans la console du navigateur :**

```javascript
✅ [Session Monitor] Démarré - Vérification toutes les 30 secondes
🔍 [Session Monitor] Status: valid
👁️ [Session Monitor] Focus détecté - Vérification immédiate
⚠️ [Session Monitor] Session invalide détectée !
```

### **Dans Laravel (storage/logs/laravel.log) :**

```
🔍 [Session Check API] Vérification {
    "guard": "web",
    "user_id": 53,
    "has_session_token": true,
    "has_db_token": true,
    "tokens_match": false
}

⚠️ [Session Check API] Session invalide détectée {
    "guard": "web",
    "user_id": 53,
    "email": "student@example.com",
    "reason": "Token mismatch - nouvelle connexion détectée"
}
```

---

## 🧪 COMMENT TESTER ?

### **TEST 1 : DÉTECTION AUTOMATIQUE**

1. **Connectez-vous sur Chrome** avec un compte
2. **Attendez 5-10 secondes**
3. **Ouvrez Firefox** et connectez-vous avec le **même compte**
4. **Observez Chrome** :
   - Dans les **30 secondes**, vous verrez le toastr
   - Puis redirection automatique vers `/login`

### **TEST 2 : DÉTECTION IMMÉDIATE (FOCUS)**

1. **Connectez-vous sur Chrome**
2. **Changez d'onglet** (ouvrez un autre onglet)
3. **Ouvrez Firefox** et connectez-vous
4. **Revenez sur l'onglet Chrome**
5. **Détection IMMÉDIATE** (< 1 seconde)

### **TEST 3 : CONSOLE DU NAVIGATEUR**

1. **Ouvrez Chrome DevTools** (F12)
2. **Onglet Console**
3. **Connectez-vous**
4. **Observez les logs** :
```
✅ [Session Monitor] Démarré
🔍 [Session Monitor] Status: valid
🔍 [Session Monitor] Status: valid
...
```

---

## ⚙️ CONFIGURATION

### **Modifier l'intervalle de vérification :**

**Fichier** : `Modules/LMS/resources/views/components/layouts/session-monitor.blade.php`

```javascript
const CHECK_INTERVAL = 30000; // 30 secondes (30000 ms)

// Pour 10 secondes :
const CHECK_INTERVAL = 10000;

// Pour 1 minute :
const CHECK_INTERVAL = 60000;
```

### **Modifier le délai de redirection :**

```javascript
// Rediriger après 2 secondes
setTimeout(() => {
    window.location.href = data.redirect || '/login';
}, 2000);

// Pour redirection immédiate :
window.location.href = data.redirect || '/login';
```

---

## 📂 FICHIERS CRÉÉS/MODIFIÉS

### **Nouveaux fichiers :**
1. ✅ `Modules/LMS/resources/views/components/layouts/session-monitor.blade.php`
2. ✅ `Modules/LMS/app/Http/Controllers/SessionCheckController.php`
3. ✅ `GUIDE_SESSION_MONITOR.md` (ce fichier)

### **Fichiers modifiés :**
1. ✅ `Modules/LMS/routes/web.php` (route `session.check`)
2. ✅ `Modules/LMS/resources/views/portals/admin/layouts/app.blade.php` (inclusion du composant)
3. ✅ `Modules/LMS/resources/views/theme/layouts/partials/footer-script.blade.php` (inclusion du composant)

---

## 🔧 DÉSACTIVER LA SURVEILLANCE

### **Temporairement (pour un utilisateur) :**

Ouvrez la **Console du navigateur** (F12) :
```javascript
// Arrêter toutes les vérifications
clearInterval(window.sessionMonitorInterval);
```

### **Définitivement (pour tous) :**

**Commentez l'inclusion du composant :**

```blade
{{-- Session Monitor - Vérification automatique de session unique --}}
{{-- <x-layouts-session-monitor /> --}}
```

---

## ❓ QUESTIONS FRÉQUENTES

### **Q1 : Pourquoi 30 secondes et pas instantané ?**
**R** : Pour équilibrer :
- ⚡ Performance (pas de surcharge serveur)
- 🔋 Batterie mobile (pas de requêtes constantes)
- ⚠️ Rapidité de détection (< 30s est acceptable)

**Note** : La détection est **immédiate** si l'utilisateur revient sur l'onglet.

### **Q2 : Et si l'utilisateur est hors ligne ?**
**R** : Le script JavaScript gère les erreurs réseau silencieusement. Quand la connexion revient, les vérifications reprennent.

### **Q3 : Ça consomme beaucoup de ressources ?**
**R** : Non ! Une requête toutes les 30 secondes est négligeable :
- Taille : ~500 bytes
- Temps : < 100ms
- Impact : Aucun

---

## 📊 COMPARAISON AVANT/APRÈS

| Aspect | AVANT | APRÈS |
|--------|-------|-------|
| **Détection** | À la prochaine action | Automatique (30s max) |
| **Message** | Lors de l'action | Proactif + redirection |
| **UX** | Confus pour l'utilisateur | Clair et prévisible |
| **Logs** | Middleware uniquement | Middleware + API |

---

## ✅ AVANTAGES

1. ✅ **Détection rapide** : Max 30 secondes
2. ✅ **UX améliorée** : Message clair avant redirection
3. ✅ **Détection immédiate** lors du retour sur l'onglet
4. ✅ **Logs détaillés** : Console + Laravel
5. ✅ **Configurable** : Intervalle ajustable
6. ✅ **Performant** : Impact minimal

---

## 🎉 CONCLUSION

Le système de session unique est maintenant **proactif** au lieu de **réactif** !

```
┌────────────────────────────────────────────┐
│ ✅ SESSION UNIQUE AVEC SURVEILLANCE        │
│                                            │
│ ⏰ Vérification automatique : ACTIF        │
│ 👁️ Détection au focus : ACTIF             │
│ 📊 Logs détaillés : ACTIF                 │
│ ⚠️ Notification toastr : ACTIF            │
│ 🚪 Déconnexion automatique : ACTIF        │
│                                            │
│ 🎯 SYSTÈME COMPLET OPÉRATIONNEL !         │
└────────────────────────────────────────────┘
```

---

**🚀 Testez maintenant avec 2 navigateurs et observez la magie ! 🎉**

