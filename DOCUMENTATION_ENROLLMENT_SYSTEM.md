# 📚 SYSTÈME D'ENROLLMENT - DOCUMENTATION COMPLÈTE

## 🎯 VUE D'ENSEMBLE

Votre système LMS utilise **2 tables principales** pour gérer les inscriptions des étudiants :

1. **`enrollments`** - Table originale (ancienne, peut-être obsolète)
2. **`purchase_details`** - Table principale actuellement utilisée

---

## 📊 STRUCTURE DES BASES DE DONNÉES

### 1️⃣ Table `purchase_details` (PRINCIPALE)

**Migration :** `2024_09_12_120434_create_purchase_details_table.php`

```sql
CREATE TABLE purchase_details (
    id BIGINT PRIMARY KEY,
    purchase_number VARCHAR,
    purchase_id BIGINT,              -- FK vers purchases
    course_id BIGINT NULLABLE,       -- FK vers courses
    bundle_id BIGINT NULLABLE,       -- FK vers course_bundles
    user_id BIGINT,                  -- FK vers users (étudiant)
    price DECIMAL NULLABLE,
    platform_fee DECIMAL,            -- Ajouté ultérieurement
    discount_price DECIMAL,          -- Ajouté ultérieurement
    details JSON,                    -- Infos supplémentaires
    type ENUM('enrolled', 'purchase'),     -- Type d'inscription
    purchase_type ENUM('bundle', 'course'),
    status ENUM('processing', 'completed', 'pending'),
    organization_id BIGINT NULLABLE, -- Ajouté pour organisations
    enrollment_link_id BIGINT NULLABLE, -- Ajouté pour liens d'inscription
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP
);
```

**Champs clés :**
- **`type`** : 
  - `'enrolled'` = Inscription gratuite ou par organisation
  - `'purchase'` = Achat payant
- **`status`** :
  - `'processing'` = En cours
  - `'completed'` = Terminé
  - `'pending'` = En attente
- **`details`** (JSON) : Contient les informations du cours/bundle

---

### 2️⃣ Table `enrollments` (ANCIENNE)

**Migration :** `2024_07_15_095640_create_enrollments_table.php`

```sql
CREATE TABLE enrollments (
    id BIGINT PRIMARY KEY,
    student_id BIGINT,               -- FK vers users
    organization_id BIGINT NULLABLE, -- FK vers users (organisation)
    course_id BIGINT,                -- FK vers courses
    course_title VARCHAR,
    price DECIMAL(10,2) NULLABLE,
    status ENUM('free', 'paid'),
    course_status ENUM('processing', 'completed'),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**⚠️ Note :** Cette table semble être l'ancienne version. Le système actuel utilise principalement `purchase_details`.

---

## 🔄 PROCESSUS D'ENROLLMENT

### **Méthode 1 : Enrollment Manuel (Admin)**

**Fichier :** `app/Http/Controllers/Admin/EnrollmentController.php`

```php
public function enrolled(Request $request)
{
    $enrolled = $this->purchase->courseEnroll($request, $request->student_id);
}
```

**Étapes :**
1. L'admin sélectionne un étudiant (`student_id`)
2. L'admin sélectionne un/des cours (`courses[]`)
3. La méthode `courseEnroll()` crée :
   - Un enregistrement dans `purchases`
   - Un enregistrement dans `purchase_details` pour chaque cours avec `type = 'enrolled'`

---

### **Méthode 2 : Enrollment Automatique (Lien d'Organisation)**

**Fichier :** `app/Repositories/Student/StudentRepository.php` (ligne 446)

```php
\Modules\LMS\Models\Purchase\PurchaseDetails::create([
    'purchase_number' => 'ORG-' . time() . '-' . $user->id,
    'purchase_id' => 0, // Pas de purchase pour les org
    'course_id' => $course->id,
    'user_id' => $user->id,
    'platform_fee' => 0,
    'price' => 0, // Gratuit
    'discount_price' => 0,
    'details' => json_encode([
        'enrollment_type' => 'organization',
        'enrollment_link_id' => $enrollmentLink->id,
        'organization_id' => $enrollmentLink->organization_id,
        'course_title' => $course->title,
        'enrollment_date' => now()->toISOString()
    ]),
    'type' => 'enrolled',
    'purchase_type' => 'course',
    'status' => 'completed',
    'organization_id' => $enrollmentLink->organization_id,
    'enrollment_link_id' => $enrollmentLink->id,
]);
```

**Étapes :**
1. Un étudiant s'inscrit via un lien d'organisation
2. Un enregistrement est créé directement dans `purchase_details`
3. `purchase_id = 0` (pas de purchase associé)
4. `type = 'enrolled'` et `status = 'completed'`

---

### **Méthode 3 : Enrollment Gratuit (Self-enrollment)**

**Fichier :** `app/Repositories/Purchase/PurchaseRepository.php` (ligne 124)

```php
public function courseEnrolled($request)
{
    if (is_free($request->id, type: $request->type ?? null)) {
        // Créer un purchase
        $purchase = $this->purchaseStore($purchaseData);
        
        // Créer purchase_details
        PurchaseDetails::create([
            'purchase_number' => strtoupper(orderNumber()),
            'purchase_id' => $purchase->id,
            'user_id' => $userId,
            'course_id' => $type === 'course' ? $courseInfo->id : null,
            'bundle_id' => $type === 'bundle' ? $courseInfo->id : null,
            'details' => $courseInfo,
            'type' => 'enrolled',
            'purchase_type' => $type === 'bundle' ? 'bundle' : 'course',
        ]);
    }
}
```

**Étapes :**
1. L'étudiant clique sur "Enroll" pour un cours gratuit
2. Un `purchase` est créé avec `type = 'enrolled'`
3. Un `purchase_details` est créé avec les infos du cours

---

## 🔍 RELATIONS ELOQUENT

### **Model `Enrollment`** (ancienne table)

```php
class Enrollment extends Model
{
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id', 'id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
```

### **Model `PurchaseDetails`** (table actuelle)

```php
class PurchaseDetails extends Model
{
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function courseBundle(): BelongsTo
    {
        return $this->belongsTo(CourseBundle::class, 'bundle_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }
}
```

---

## 📝 RÉSUMÉ DES DIFFÉRENCES

| Caractéristique | `enrollments` | `purchase_details` |
|-----------------|---------------|-------------------|
| **Utilisation** | Ancienne | **Actuelle** ✅ |
| **Support Bundles** | ❌ Non | ✅ Oui |
| **Organisation Link** | ❌ Non | ✅ Oui |
| **Soft Deletes** | ❌ Non | ✅ Oui |
| **Platform Fee** | ❌ Non | ✅ Oui |
| **JSON Details** | ❌ Non | ✅ Oui |
| **Type Purchase** | ❌ Non | ✅ Oui (`enrolled`/`purchase`) |

---

## 🎯 REQUÊTES UTILES

### **Récupérer tous les enrollments d'un étudiant**

```php
$enrollments = PurchaseDetails::where('user_id', $userId)
    ->where('type', 'enrolled')
    ->with('course', 'courseBundle')
    ->get();
```

### **Vérifier si un étudiant est enrollé dans un cours**

```php
$isEnrolled = PurchaseDetails::where('user_id', $userId)
    ->where('course_id', $courseId)
    ->where('type', 'enrolled')
    ->exists();
```

### **Récupérer les enrollments avec statut completed**

```php
$completed = PurchaseDetails::where('user_id', $userId)
    ->where('type', 'enrolled')
    ->where('status', 'completed')
    ->get();
```

### **Enrollments via organisation**

```php
$orgEnrollments = PurchaseDetails::where('user_id', $userId)
    ->whereNotNull('organization_id')
    ->whereNotNull('enrollment_link_id')
    ->with('course')
    ->get();
```

---

## ⚠️ POINTS IMPORTANTS

1. **Table Principale** : `purchase_details` est la table à utiliser
2. **Type = 'enrolled'** : Tous les enrollments (gratuits ou payants) utilisent ce type
3. **Purchase ID = 0** : Pour les enrollments d'organisation (pas de purchase associé)
4. **Details JSON** : Contient les informations supplémentaires (titre, date, organisation, etc.)
5. **Soft Deletes** : Les enrollments supprimés sont conservés avec `deleted_at`

---

## 🔧 MAINTENANCE

### **Migrer de `enrollments` vers `purchase_details`**

Si vous avez encore des données dans l'ancienne table :

```php
use Modules\LMS\Models\Enrollment;
use Modules\LMS\Models\Purchase\PurchaseDetails;

$oldEnrollments = Enrollment::all();

foreach ($oldEnrollments as $enrollment) {
    PurchaseDetails::create([
        'purchase_number' => 'MIGR-' . time() . '-' . $enrollment->id,
        'purchase_id' => 0,
        'course_id' => $enrollment->course_id,
        'user_id' => $enrollment->student_id,
        'price' => $enrollment->price ?? 0,
        'details' => json_encode([
            'course_title' => $enrollment->course_title,
            'migrated_from' => 'enrollments',
            'original_id' => $enrollment->id
        ]),
        'type' => 'enrolled',
        'purchase_type' => 'course',
        'status' => $enrollment->course_status,
        'organization_id' => $enrollment->organization_id,
        'created_at' => $enrollment->created_at,
        'updated_at' => $enrollment->updated_at,
    ]);
}
```

---

✅ **Votre système d'enrollment est maintenant complètement documenté !**

