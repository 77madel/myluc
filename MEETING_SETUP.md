# Configuration des Liens de Réunion Réels

## 🎯 Vue d'ensemble

Ce système génère de vrais liens de réunion fonctionnels pour les webinaires en utilisant les APIs officielles de Microsoft Teams, Zoom et Google Meet.

## 🔧 Configuration Requise

### 1. Microsoft Teams (Recommandé)

#### Étapes de configuration :

1. **Créer une application Azure AD**
   - Aller sur [Azure Portal](https://portal.azure.com)
   - Azure Active Directory > App registrations > New registration
   - Nom : "MyLuc LMS Meeting App"
   - Type : Accounts in this organizational directory only
   - Redirect URI : `http://localhost/auth/microsoft/callback`

2. **Configurer les permissions**
   - API permissions > Add a permission > Microsoft Graph > Application permissions
   - Ajouter : `OnlineMeetings.ReadWrite`
   - Grant admin consent

3. **Créer un secret client**
   - Certificates & secrets > New client secret
   - Description : "Meeting App Secret"
   - Expires : 24 months
   - Copier la valeur du secret

4. **Variables d'environnement**
   ```env
   MICROSOFT_TENANT_ID=your-tenant-id
   MICROSOFT_CLIENT_ID=your-client-id
   MICROSOFT_CLIENT_SECRET=your-client-secret
   MICROSOFT_ORGANIZER_ID=me
   ```

### 2. Zoom (Optionnel)

#### Étapes de configuration :

1. **Créer une app JWT dans Zoom**
   - Aller sur [Zoom Marketplace](https://marketplace.zoom.us)
   - Develop > Build App > JWT
   - App Name : "MyLuc LMS"
   - Company Name : "Votre entreprise"
   - Developer Email : votre email

2. **Configurer l'app**
   - Scopes : `meeting:write:admin`, `meeting:read:admin`
   - Activation : Activer l'app
   - Copier API Key et API Secret

3. **Variables d'environnement**
   ```env
   ZOOM_API_KEY=your-zoom-api-key
   ZOOM_API_SECRET=your-zoom-api-secret
   ```

### 3. Google Meet (Fonctionne immédiatement)

Google Meet ne nécessite pas de configuration API. Les liens générés sont immédiatement fonctionnels.

## 🚀 Utilisation

### Génération automatique lors de la publication

Quand un webinaire est publié, un lien Teams est automatiquement généré.

### Génération manuelle

```php
$realMeetingService = new \App\Services\RealMeetingService();

// Teams
$teamsMeeting = $realMeetingService->createTeamsMeeting($webinar);

// Zoom
$zoomMeeting = $realMeetingService->createZoomMeeting($webinar);

// Google Meet
$meetLink = $realMeetingService->createGoogleMeetLink($webinar);
```

### Interface Admin

1. Aller dans Admin > Webinaires
2. Cliquer sur "Actions" > "Générer Lien de Réunion"
3. Choisir la plateforme (Teams/Zoom/Meet)
4. Le lien est généré et sauvegardé

## 🔍 Test des Liens

### Commande de test

```bash
# Tester avec un webinaire spécifique
php artisan webinar:test-real-meeting-links 19

# Tester avec le premier webinaire
php artisan webinar:test-real-meeting-links
```

### Vérification manuelle

1. **Teams** : Cliquer sur le lien généré
2. **Zoom** : Utiliser l'ID et le mot de passe
3. **Google Meet** : Le lien fonctionne immédiatement

## 🛠️ Dépannage

### Erreurs communes

1. **"Impossible d'obtenir le token d'accès Microsoft"**
   - Vérifier MICROSOFT_TENANT_ID, CLIENT_ID, CLIENT_SECRET
   - Vérifier les permissions dans Azure AD

2. **"Clés API Zoom non configurées"**
   - Configurer ZOOM_API_KEY et ZOOM_API_SECRET
   - Vérifier que l'app Zoom est activée

3. **Liens ne fonctionnent pas**
   - Vérifier les logs : `storage/logs/laravel.log`
   - Tester avec Google Meet (fonctionne toujours)

### Logs

Les logs sont dans `storage/logs/laravel.log` :
- Génération de liens réussie
- Erreurs d'API
- Tokens d'accès

## 📊 Fonctionnalités

### Microsoft Teams
- ✅ Liens de réunion programmés
- ✅ Salle d'attente configurable
- ✅ Chat activé
- ✅ Enregistrement possible
- ✅ Intégration calendrier

### Zoom
- ✅ Réunions programmées
- ✅ Salle d'attente
- ✅ Enregistrement cloud
- ✅ Contrôles host/participant
- ✅ Rapports d'assiduité

### Google Meet
- ✅ Liens instantanés
- ✅ Pas de configuration requise
- ✅ Intégration Google Workspace
- ✅ Enregistrement possible

## 🔒 Sécurité

- Tokens d'accès avec expiration
- Secrets clients sécurisés
- Permissions minimales requises
- Logs d'audit des générations

## 📈 Monitoring

- Logs de génération de liens
- Statistiques d'utilisation
- Erreurs d'API trackées
- Performance des APIs





