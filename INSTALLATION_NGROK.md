# 🚀 INSTALLATION ET CONFIGURATION NGROK

## 📥 **ÉTAPE 1 : TÉLÉCHARGER NGROK**

1. **Aller sur** : https://ngrok.com/download
2. **Choisir** : Windows (64-bit)
3. **Télécharger** le fichier ZIP
4. **Extraire** dans un dossier (ex: `C:\ngrok`)

---

## ⚙️ **ÉTAPE 2 : LANCER NGROK**

### **Ouvrir un NOUVEAU terminal PowerShell** :

```powershell
# Aller dans le dossier ngrok
cd C:\ngrok

# Lancer ngrok sur le port 8000
.\ngrok http 8000
```

**IMPORTANT** : Gardez cette fenêtre PowerShell **OUVERTE** !

### **Résultat attendu** :

```
ngrok

Session Status                online
Account                       your-account (Plan: Free)
Version                       3.x.x
Region                        United States (us)
Latency                       50ms
Web Interface                 http://127.0.0.1:4040
Forwarding                    https://abc123.ngrok-free.app -> http://localhost:8000

Connections                   ttl     opn     rt1     rt5     p50     p90
                              0       0       0.00    0.00    0.00    0.00
```

### **Noter l'URL** :
```
https://abc123.ngrok-free.app
```
☝️ **COPIEZ CETTE URL !** Elle sera différente à chaque fois.

---

## 🔧 **ÉTAPE 3 : METTRE À JOUR .ENV**

**Ouvrir** : `C:\Users\madou\OneDrive\Desktop\ProjetLaravel\myluc\.env`

**Modifier ces lignes** :

```env
# Remplacer
APP_URL=http://127.0.0.1:8000
LINKEDIN_REDIRECT_URI=http://127.0.0.1:8000/linkedin/callback

# Par (avec VOTRE URL ngrok)
APP_URL=https://abc123.ngrok-free.app
LINKEDIN_REDIRECT_URI=https://abc123.ngrok-free.app/linkedin/callback
```

**⚠️ IMPORTANT** : Remplacez `abc123` par votre vraie URL ngrok !

---

## 🔵 **ÉTAPE 4 : CONFIGURER LINKEDIN**

1. **Aller sur** : https://www.linkedin.com/developers/apps
2. **Ouvrir** votre application
3. **Onglet "Settings"** :
   - **Website URL** : `https://abc123.ngrok-free.app`
   - **Privacy Policy URL** : `https://abc123.ngrok-free.app/privacy-policy`
4. **Onglet "Auth"** → **OAuth 2.0 settings** :
   - **Redirect URLs** : Ajouter `https://abc123.ngrok-free.app/linkedin/callback`
5. **Cliquer sur "Update"**

---

## 🎯 **ÉTAPE 5 : ACTIVER "SHARE ON LINKEDIN"**

1. **Onglet "Products"**
2. **Trouver** : "Share on LinkedIn"
3. **Cliquer** sur "Request access" (ou "Select" si disponible)
4. **Remplir** :
   - Verification URL : `https://abc123.ngrok-free.app/privacy-policy`
5. **Soumettre**

LinkedIn devrait valider instantanément car votre site est maintenant accessible !

---

## 🔄 **ÉTAPE 6 : REDÉMARRER LE SERVEUR**

**Dans le terminal où tourne `php artisan serve`** :

```powershell
# Ctrl+C pour arrêter
# Puis :
php artisan config:clear
php artisan serve
```

---

## 🧪 **ÉTAPE 7 : TESTER !**

1. **Aller sur** : `https://abc123.ngrok-free.app` (VOTRE URL ngrok)
2. **Se connecter** en tant qu'étudiant
3. **Aller sur** : Certificats
4. **Cliquer** sur LinkedIn 🔵
5. **Connecter LinkedIn et Publier**
6. **Autoriser**
7. ✅ **POST PUBLIÉ ET ACCESSIBLE PAR TOUS ! 🎉**

---

## 📊 **RÉSUMÉ DES URLs**

| Service | URL Avant | URL Après (avec ngrok) |
|---------|-----------|------------------------|
| **Votre site** | http://127.0.0.1:8000 | https://abc123.ngrok-free.app |
| **Privacy Policy** | http://127.0.0.1:8000/privacy-policy | https://abc123.ngrok-free.app/privacy-policy |
| **Callback LinkedIn** | http://127.0.0.1:8000/linkedin/callback | https://abc123.ngrok-free.app/linkedin/callback |

---

## ⚠️ **NOTES IMPORTANTES**

### **1. ngrok Free Plan**

- ✅ Gratuit
- ⚠️ URL change à chaque redémarrage
- ⚠️ Limite de sessions
- ✅ Parfait pour tester !

### **2. Après chaque redémarrage de ngrok**

Si vous redémarrez ngrok, l'URL change :
1. Noter la nouvelle URL
2. Mettre à jour `.env`
3. Mettre à jour LinkedIn Developers
4. Redémarrer le serveur : `php artisan serve`

### **3. Garder 2 terminaux ouverts**

**Terminal 1** : ngrok
```
.\ngrok http 8000
```

**Terminal 2** : Laravel
```
php artisan serve
```

---

## 🎁 **AVANTAGES AVEC NGROK**

✅ Site accessible publiquement (temporairement)  
✅ LinkedIn peut vérifier votre site  
✅ "Share on LinkedIn" activé  
✅ Posts publics et accessibles par tous  
✅ Certificat partagé avec image  

---

## 📝 **CHECKLIST**

- [ ] Télécharger ngrok
- [ ] Lancer `.\ngrok http 8000`
- [ ] Noter l'URL ngrok
- [ ] Mettre à jour `.env`
- [ ] Configurer LinkedIn Developers
- [ ] Activer "Share on LinkedIn"
- [ ] Redémarrer le serveur
- [ ] Tester le partage
- [ ] ✅ Partage public fonctionnel !

**Suivez ces étapes et votre système sera 100% opérationnel ! 🚀**

