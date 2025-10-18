# Configuration Paydunya

## 1. Obtenir les clés Paydunya

### Pour le mode TEST (Sandbox) :
1. Allez sur : https://paydunya.com
2. Créez un compte développeur
3. Accédez à votre dashboard
4. Générez vos clés de test

### Pour le mode PRODUCTION :
1. Complétez la vérification de votre compte
2. Soumettez vos documents commerciaux
3. Obtenez vos clés de production

## 2. Configuration dans .env

```env
# Configuration Paydunya
PAYDUNYA_TEST_MODE=true
PAYDUNYA_MASTER_KEY=votre_vraie_master_key_ici
PAYDUNYA_PRIVATE_KEY=votre_vraie_private_key_ici
PAYDUNYA_TOKEN=votre_vrai_token_ici
PAYDUNYA_CURRENCY=XOF
PAYDUNYA_PHONE=+22373982334
PAYDUNYA_ADDRESS="Votre adresse commerciale"
PAYDUNYA_TAGLINE="Votre slogan"
```

## 3. Clés de test Paydunya (pour développement)

Si vous voulez tester avec des clés de démonstration :

```env
PAYDUNYA_TEST_MODE=true
PAYDUNYA_MASTER_KEY=test_master_key
PAYDUNYA_PRIVATE_KEY=test_private_key
PAYDUNYA_TOKEN=test_token
```

## 4. Vérification de la configuration

Après avoir mis à jour votre .env, redémarrez votre serveur :

```bash
php artisan config:clear
php artisan cache:clear
```

## 5. Test de la configuration

Vous pouvez tester votre configuration avec :

```php
// Dans tinker
php artisan tinker

// Test de la configuration
$config = config('paydunya');
dd($config);
```
