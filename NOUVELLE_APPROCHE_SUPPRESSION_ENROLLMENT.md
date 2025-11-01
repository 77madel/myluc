# 🗑️ NOUVELLE APPROCHE - Suppression de l'Enrollment Après Certificat

## 🎯 CHANGEMENT D'APPROCHE

### **❌ ANCIENNE MÉTHODE (abandonnée) :**
- Ajouter un champ `is_locked` dans `purchase_details`
- Verrouiller l'enrollment (mais il reste dans la base)
- L'étudiant voit toujours le cours mais ne peut pas y accéder

### **✅ NOUVELLE MÉTHODE (actuelle) :**
- **SUPPRIMER** l'enrollment après l'obtention du certificat
- Utilise le **soft delete** (suppression douce)
- L'enrollment disparaît de la liste de l'étudiant
- Peut être **restauré** facilement en cas de réinscription

---

## 🔄 COMMENT ÇA FONCTIONNE MAINTENANT

### **1️⃣ Étudiant Termine un Cours**

**Fichier :** `CertificateService.php` (lignes 155-171)

```php
// Après la génération du certificat
$enrollment = PurchaseDetails::where('user_id', $userId)
    ->where('course_id', $courseId)
    ->where('type', 'enrolled')
    ->first();

if ($enrollment) {
    // SOFT DELETE (conserve les données avec deleted_at)
    $enrollment->delete();
    Log::info("🗑️ Enrollment supprimé - Certificat obtenu");
}
```

**Résultat :**
- ✅ Certificat généré dans `user_certificates`
- ✅ Enrollment supprimé (`deleted_at` rempli)
- ✅ L'étudiant **ne voit plus le cours** dans sa liste
- ✅ Les données sont **conservées** (soft delete)

---

### **2️⃣ Étudiant Essaie d'Accéder au Cours**

**Fichier :** `CourseController.php` (lignes 91-104)

```php
// Vérifier si l'enrollment existe (non supprimé)
$purchaseDetails = PurchaseDetails::where('user_id', $userId)
    ->where('course_id', $courseId)
    ->where('type', 'enrolled')
    ->first();

if (!$purchaseDetails) {
    // Vérifier si l'étudiant a obtenu un certificat
    $hasCertificate = UserCertificate::where('user_id', $userId)
        ->where('course_id', $courseId)
        ->exists();
    
    if ($hasCertificate) {
        // Message spécifique pour ceux qui ont le certificat
        return redirect()->with('warning', 
            'Vous avez déjà obtenu le certificat pour ce cours. 
             Contactez un administrateur pour une réinscription.');
    }
    
    // Sinon, message standard
    return redirect()->with('error', 
        'Vous n\'êtes pas inscrit à ce cours.');
}
```

**Résultats possibles :**
- ✅ **Enrollment actif** → Accès au cours
- ❌ **Pas d'enrollment + pas de certificat** → "Vous n'êtes pas inscrit"
- ❌ **Pas d'enrollment + certificat obtenu** → "Vous avez déjà obtenu le certificat"

---

### **3️⃣ Admin Réinscrit un Étudiant**

**Fichier :** `PurchaseRepository.php` (lignes 177-251)

```php
public function reEnrollStudent($userId, $courseId): array
{
    // 1. Chercher l'enrollment supprimé (avec soft delete)
    $deletedPurchase = PurchaseDetails::withTrashed()
        ->where('user_id', $userId)
        ->where('course_id', $courseId)
        ->where('type', 'enrolled')
        ->first();

    if ($deletedPurchase && $deletedPurchase->trashed()) {
        // Option A : RESTAURER l'enrollment supprimé
        $deletedPurchase->restore();
        $deletedPurchase->update(['status' => 'processing']);
        
    } else {
        // Option B : CRÉER un nouvel enrollment
        PurchaseDetails::create([
            'purchase_number' => 'RE-' . orderNumber(),
            'purchase_id' => $purchase->id,
            'user_id' => $userId,
            'course_id' => $courseId,
            'details' => $course,
            'type' => 'enrolled',
            'purchase_type' => 'course',
            'status' => 'processing',
        ]);
    }

    // 2. Réinitialiser la progression
    TopicProgress::where('user_id', $userId)
        ->where('course_id', $courseId)
        ->update(['status' => 'not_started']);

    ChapterProgress::where('user_id', $userId)
        ->where('course_id', $courseId)
        ->update(['status' => 'not_started']);

    return ['status' => 'success'];
}
```

**Deux scénarios :**
- **Si enrollment supprimé trouvé** → Le **restaurer** (retire `deleted_at`)
- **Si rien trouvé** → Créer un **nouvel enrollment**

---

## 📊 BASE DE DONNÉES

### **Table `purchase_details` :**

| Champ | Description |
|-------|-------------|
| `deleted_at` | Si NULL = actif, si rempli = supprimé (soft delete) |
| `status` | `processing` = en cours, `completed` = terminé |

### **Requêtes SQL utiles :**

**Voir les enrollments actifs :**
```sql
SELECT * FROM purchase_details 
WHERE deleted_at IS NULL 
AND type = 'enrolled';
```

**Voir les enrollments supprimés (certificats obtenus) :**
```sql
SELECT * FROM purchase_details 
WHERE deleted_at IS NOT NULL 
AND type = 'enrolled';
```

**Restaurer un enrollment manuellement :**
```sql
UPDATE purchase_details 
SET deleted_at = NULL, 
    status = 'processing' 
WHERE user_id = 123 
AND course_id = 45;
```

---

## ✅ AVANTAGES DE CETTE APPROCHE

| Avantage | Description |
|----------|-------------|
| **🧹 Plus propre** | L'étudiant ne voit plus le cours dans sa liste |
| **📦 Données conservées** | Soft delete garde tout l'historique |
| **🔄 Restauration facile** | Un simple `restore()` suffit |
| **🎯 Plus simple** | Pas besoin du champ `is_locked` |
| **📊 Statistiques** | On peut compter les enrollments supprimés = certificats obtenus |

---

## 🔍 VÉRIFICATIONS

### **Vérifier les enrollments supprimés (certificats obtenus) :**

```php
use Modules\LMS\Models\Purchase\PurchaseDetails;

// Tous les enrollments supprimés
$deleted = PurchaseDetails::onlyTrashed()
    ->where('type', 'enrolled')
    ->with('user', 'course')
    ->get();

foreach ($deleted as $enrollment) {
    echo "Étudiant : " . $enrollment->user->email . "\n";
    echo "Cours : " . $enrollment->course->title . "\n";
    echo "Supprimé le : " . $enrollment->deleted_at . "\n";
    echo "---\n";
}
```

### **Vérifier si un étudiant a fini un cours :**

```php
// Option 1 : Vérifier le certificat
$hasCertificate = UserCertificate::where('user_id', $userId)
    ->where('course_id', $courseId)
    ->exists();

// Option 2 : Vérifier l'enrollment supprimé
$hasFinished = PurchaseDetails::onlyTrashed()
    ->where('user_id', $userId)
    ->where('course_id', $courseId)
    ->where('type', 'enrolled')
    ->exists();
```

---

## 📝 WORKFLOW COMPLET

```
┌──────────────────────────────────────────┐
│ 1. Étudiant termine le dernier topic    │
└────────────────┬─────────────────────────┘
                 │
                 ▼
┌──────────────────────────────────────────┐
│ 2. CertificateService génère certificat │
└────────────────┬─────────────────────────┘
                 │
                 ▼
┌──────────────────────────────────────────┐
│ 3. Enrollment SUPPRIMÉ (soft delete)    │
│    → deleted_at = now()                  │
└────────────────┬─────────────────────────┘
                 │
                 ▼
┌──────────────────────────────────────────┐
│ 4. Cours disparaît de la liste étudiant │
└────────────────┬─────────────────────────┘
                 │
                 ▼
┌──────────────────────────────────────────┐
│ 5. Si étudiant tente d'accéder :        │
│    → Message "Certificat déjà obtenu"    │
└────────────────┬─────────────────────────┘
                 │
                 ▼
┌──────────────────────────────────────────┐
│ 6. Admin peut réinscrire :               │
│    → restore() ou create()               │
│    → Réinitialise la progression         │
└──────────────────────────────────────────┘
```

---

## 🎯 RÉSUMÉ

| Action | Avant (is_locked) | Maintenant (delete) |
|--------|-------------------|---------------------|
| **Certificat obtenu** | `is_locked = true` | `deleted_at = now()` |
| **Vue étudiant** | Cours visible mais bloqué | Cours invisible |
| **Base de données** | Enrollment présent | Enrollment soft-deleted |
| **Réinscription** | `is_locked = false` | `restore()` ou nouveau |
| **Historique** | Dans `locked_at` | Dans `deleted_at` |

---

## ⚠️ NOTES IMPORTANTES

1. ✅ Le **soft delete** est activé sur `PurchaseDetails` (trait `SoftDeletes`)
2. ✅ Les données ne sont **jamais vraiment supprimées**
3. ✅ On peut toujours voir l'historique avec `withTrashed()`
4. ✅ Le certificat reste dans `user_certificates` (jamais supprimé)
5. ✅ La progression (topics, chapters) reste dans la base

---

## 🎉 **APPROCHE PLUS SIMPLE ET PLUS PROPRE !**

Fini les champs `is_locked`, `locked_at`, `lock_reason` ! 
On utilise simplement le **soft delete** natif de Laravel ! 🚀

