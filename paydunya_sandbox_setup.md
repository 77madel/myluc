# Configuration Paydunya Sandbox

## Étapes pour obtenir les clés sandbox

### 1. Créer un compte Paydunya
1. Allez sur : https://paydunya.com
2. Cliquez sur "S'inscrire" ou "Créer un compte"
3. Remplissez le formulaire d'inscription
4. Vérifiez votre email

### 2. Accéder au dashboard
1. Connectez-vous à votre compte
2. Allez dans "Développement" ou "API"
3. Activez le mode "Sandbox" ou "Test"

### 3. Générer les clés sandbox
1. Dans le mode sandbox, générez :
   - **Master Key** (clé principale)
   - **Private Key** (clé privée)
   - **Token** (jeton d'authentification)

### 4. Configuration dans .env

Remplacez vos clés actuelles par les vraies clés sandbox :

```env
# Configuration Paydunya Sandbox
PAYDUNYA_TEST_MODE=true
PAYDUNYA_MASTER_KEY=votre_vraie_master_key_sandbox
PAYDUNYA_PRIVATE_KEY=votre_vraie_private_key_sandbox
PAYDUNYA_TOKEN=votre_vrai_token_sandbox
PAYDUNYA_CURRENCY=XOF
PAYDUNYA_PHONE=+22373982334
PAYDUNYA_ADDRESS="Votre adresse commerciale"
PAYDUNYA_TAGLINE="Votre slogan"
```

### 5. Clés de test Paydunya (si vous ne pouvez pas créer un compte)

Si vous ne pouvez pas créer un compte Paydunya immédiatement, vous pouvez utiliser ces clés de test temporaires :

```env
# Clés de test temporaires (remplacez par vos vraies clés)
PAYDUNYA_TEST_MODE=true
PAYDUNYA_MASTER_KEY=sandbox_master_key_12345
PAYDUNYA_PRIVATE_KEY=sandbox_private_key_67890
PAYDUNYA_TOKEN=sandbox_token_abcdef
```

### 6. Vérification

Après avoir mis à jour votre .env :

```bash
# Redémarrer le serveur
php artisan config:clear
php artisan cache:clear

# Tester la configuration
php artisan tinker
>>> config('paydunya.master_key')
```

## Important

- Les clés sandbox sont différentes des clés de production
- Le mode sandbox ne débite pas d'argent réel
- Vous devez utiliser les vraies clés de votre compte Paydunya
- Les clés que vous avez actuellement ne sont pas valides
