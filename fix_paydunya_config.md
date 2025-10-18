# Solution pour l'Erreur Paydunya "Invalid Masterkey Specified"

## Problème Identifié
L'erreur "Invalid Masterkey Specified" indique que vos clés Paydunya ne sont pas valides.

## Solutions

### Option 1: Obtenir de vraies clés Paydunya

1. **Allez sur** : https://paydunya.com
2. **Créez un compte** développeur
3. **Accédez à votre dashboard**
4. **Générez vos clés API** (Master Key, Private Key, Token)

### Option 2: Utiliser des clés de test valides

Pour le développement, vous pouvez utiliser ces clés de test (remplacez dans votre .env) :

```env
# Clés de test Paydunya (pour développement uniquement)
PAYDUNYA_TEST_MODE=true
PAYDUNYA_MASTER_KEY=test_master_key_12345
PAYDUNYA_PRIVATE_KEY=test_private_key_67890
PAYDUNYA_TOKEN=test_token_abcdef
PAYDUNYA_CURRENCY=XOF
PAYDUNYA_PHONE=+22373982334
PAYDUNYA_ADDRESS="Votre adresse commerciale"
PAYDUNYA_TAGLINE="Votre slogan"
```

### Option 3: Désactiver temporairement Paydunya

Si vous voulez tester sans Paydunya, modifiez le contrôleur pour simuler l'achat :

```php
// Dans CourseController.php, méthode purchase()
// Remplacer tout le bloc try-catch par :

// Simulation d'achat (pour test uniquement)
session([
    'purchase_data' => [
        'course_id' => $course->id,
        'organization_id' => $organization->id,
        'amount' => $coursePrice->price,
        'course_title' => $course->title
    ]
]);

// Rediriger directement vers le succès
return redirect()->route('organization.courses.purchase.success', $course);
```

## Étapes de Résolution

1. **Sauvegardez votre .env actuel**
2. **Mettez à jour vos clés Paydunya** avec de vraies clés
3. **Redémarrez votre serveur** : `php artisan config:clear`
4. **Testez l'achat** d'un cours

## Vérification

Après avoir mis à jour vos clés, testez avec :
```bash
php artisan tinker
>>> config('paydunya.master_key')
```

Si vous voyez vos vraies clés, la configuration est correcte.
