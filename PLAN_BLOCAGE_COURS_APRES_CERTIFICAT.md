# 🔒 PLAN : Bloquer l'Accès au Cours Après Obtention du Certificat

## 🎯 OBJECTIF

Quand un étudiant obtient un certificat pour un cours :
- ❌ Il ne peut **plus accéder** au contenu du cours
- ✅ Il peut **re-accéder** UNIQUEMENT si on le **réinscrit** manuellement

---

## 📊 SITUATION ACTUELLE

### **Comment les certificats sont générés ?**

**Fichier :** `app/Services/CertificateService.php`

1. Quand un étudiant termine le **dernier topic** d'un cours
2. Le système vérifie si **tous les chapitres et topics sont complétés**
3. Si oui → Un certificat est **généré automatiquement**
4. Le certificat est enregistré dans la table **`user_certificates`**

```php
// Ligne 143 de CertificateService.php
$userCertificate = UserCertificate::create([
    'user_id' => $userId,
    'course_id' => $courseId,
    'certificate_id' => $certificateId,  // Ex: LUC-2025-ABC123
    'type' => 'course',
    'subject' => $course->title,
    'certificate_content' => $certificateContent,
    'certificated_date' => now(),
]);
```

### **Comment l'accès aux cours est contrôlé actuellement ?**

**Fichier :** `app/Http/Controllers/Frontend/CourseController.php` (ligne 74)

```php
public function courseVideoPlayer($slug, Request $request)
{
    $course = $this->course->courseDetail($slug);
    
    // Vérifier si l'étudiant est inscrit (via PurchaseDetails)
    $purchase = PurchaseRepository::getByUserId([
        'user_id' => authCheck()->id,
        'course_id' => $course->id
    ]);

    // Si pas d'inscription → BLOQUER l'accès
    if (!$purchase  && isStudent()) {
        return redirect()->back();
    }
    
    return view('theme::course.course-video', compact('course', 'assignments', 'data'));
}
```

**Actuellement :**
- ✅ Le système vérifie si l'étudiant a un enregistrement dans `purchase_details`
- ❌ Mais il **ne vérifie PAS** s'il a déjà obtenu un certificat

---

## 🛠️ SOLUTION PROPOSÉE

### **Option 1 : Ajouter un champ dans `purchase_details` (RECOMMANDÉ)**

#### **Étape 1 : Ajouter un champ `is_locked` dans `purchase_details`**

**Migration :** `add_is_locked_to_purchase_details_table.php`

```php
Schema::table('purchase_details', function (Blueprint $table) {
    $table->boolean('is_locked')->default(false)->after('status');
    $table->timestamp('locked_at')->nullable()->after('is_locked');
    $table->string('lock_reason')->nullable()->after('locked_at');
});
```

**Champs ajoutés :**
- `is_locked` (bool) → Si `true`, l'étudiant ne peut plus accéder
- `locked_at` (timestamp) → Date du verrouillage
- `lock_reason` (string) → Raison (ex: "certificate_obtained")

#### **Étape 2 : Modifier `CertificateService` pour verrouiller l'accès**

**Fichier :** `app/Services/CertificateService.php` (après ligne 151)

```php
// Après la création du certificat (ligne 151)
$userCertificate = UserCertificate::create([...]);

// VERROUILLER l'accès au cours
\Modules\LMS\Models\Purchase\PurchaseDetails::where('user_id', $userId)
    ->where('course_id', $courseId)
    ->where('type', 'enrolled')
    ->update([
        'is_locked' => true,
        'locked_at' => now(),
        'lock_reason' => 'certificate_obtained',
    ]);

Log::info("🔒 Accès au cours verrouillé pour l'utilisateur {$userId}");
```

#### **Étape 3 : Modifier le contrôle d'accès**

**Fichier :** `app/Http/Controllers/Frontend/CourseController.php`

```php
public function courseVideoPlayer($slug, Request $request)
{
    $course = $this->course->courseDetail($slug);
    
    // Vérifier si l'étudiant est inscrit
    $purchase = PurchaseRepository::getByUserId([
        'user_id' => authCheck()->id,
        'course_id' => $course->id
    ]);

    // NOUVELLE VÉRIFICATION : Bloquer si is_locked = true
    if (!$purchase || ($purchase->is_locked && isStudent())) {
        return redirect()->route('student.dashboard')
            ->with('error', 'Vous avez déjà obtenu le certificat pour ce cours. Contactez un administrateur pour une réinscription.');
    }
    
    return view('theme::course.course-video', compact('course', 'assignments', 'data'));
}
```

#### **Étape 4 : Permettre la réinscription (Admin)**

**Fichier :** `app/Repositories/Purchase/PurchaseRepository.php`

Ajouter une méthode pour réinscrire un étudiant :

```php
/**
 * Réinscrire un étudiant dans un cours (déverrouiller)
 */
public function reEnrollStudent($userId, $courseId): array
{
    try {
        // Trouver l'enrollment verrouillé
        $purchase = PurchaseDetails::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->where('type', 'enrolled')
            ->first();

        if (!$purchase) {
            return [
                'status' => 'error',
                'message' => 'Aucune inscription trouvée pour cet étudiant.'
            ];
        }

        // Déverrouiller l'accès
        $purchase->update([
            'is_locked' => false,
            'locked_at' => null,
            'lock_reason' => null,
            'status' => 'processing', // Remettre en cours
        ]);

        // Réinitialiser la progression (optionnel)
        \Modules\LMS\Models\TopicProgress::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->update(['status' => 'not_started']);

        \Modules\LMS\Models\ChapterProgress::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->update(['status' => 'not_started']);

        Log::info("🔓 Étudiant {$userId} réinscrit au cours {$courseId}");

        return [
            'status' => 'success',
            'message' => 'Étudiant réinscrit avec succès !'
        ];

    } catch (\Exception $e) {
        Log::error("❌ Erreur lors de la réinscription: " . $e->getMessage());
        return [
            'status' => 'error',
            'message' => 'Une erreur est survenue.'
        ];
    }
}
```

---

### **Option 2 : Utiliser `status` dans `purchase_details` (SIMPLE)**

Si vous ne voulez pas ajouter de champ, vous pouvez utiliser le champ `status` existant :

- `status = 'processing'` → Cours en cours ✅
- `status = 'completed'` → Cours terminé (certificat obtenu) ❌ BLOQUÉ
- `status = 'pending'` → En attente

**Avantage :** Pas de migration nécessaire  
**Inconvénient :** Moins de flexibilité

#### **Modification dans `CertificateService.php` :**

```php
// Après la création du certificat
\Modules\LMS\Models\Purchase\PurchaseDetails::where('user_id', $userId)
    ->where('course_id', $courseId)
    ->where('type', 'enrolled')
    ->update([
        'status' => 'completed', // Marquer comme complété
    ]);
```

#### **Modification dans `CourseController.php` :**

```php
if (!$purchase || ($purchase->status === 'completed' && isStudent())) {
    return redirect()->back()
        ->with('error', 'Ce cours est terminé. Vous avez déjà obtenu le certificat.');
}
```

---

## 📋 RÉSUMÉ DES MODIFICATIONS

### **Fichiers à modifier :**

1. **Migration (Option 1)** :
   - `database/migrations/XXXX_add_is_locked_to_purchase_details_table.php`

2. **CertificateService.php** :
   - Ajouter le verrouillage après la génération du certificat

3. **CourseController.php** :
   - Ajouter la vérification `is_locked` ou `status === 'completed'`

4. **PurchaseRepository.php** :
   - Ajouter la méthode `reEnrollStudent()` pour la réinscription

5. **EnrollmentController.php (Admin)** :
   - Ajouter une route/vue pour réinscrire un étudiant

---

## 🎯 WORKFLOW COMPLET

### **Scénario 1 : Étudiant termine un cours**

1. L'étudiant complète le dernier topic
2. `CertificateService::generateCertificate()` est appelé
3. Un certificat est créé dans `user_certificates`
4. L'enrollment est **verrouillé** dans `purchase_details` (`is_locked = true`)
5. L'étudiant **ne peut plus accéder** au cours

### **Scénario 2 : Admin réinscrit un étudiant**

1. L'admin va dans "Enrollments" > "Réinscrire"
2. Il sélectionne l'étudiant et le cours
3. `reEnrollStudent()` est appelé
4. `is_locked` passe à `false`
5. L'étudiant **peut à nouveau accéder** au cours

---

## 💡 RECOMMANDATION

**J'recommande l'Option 1 (ajouter `is_locked`)** car :
- ✅ Plus **clair** et **explicite**
- ✅ Permet de garder l'historique (date de verrouillage, raison)
- ✅ Ne modifie pas la signification de `status`
- ✅ Plus **flexible** pour l'avenir

---

## 🚀 PROCHAINES ÉTAPES

1. **Créer la migration** pour ajouter `is_locked`
2. **Modifier `CertificateService.php`** pour verrouiller
3. **Modifier `CourseController.php`** pour bloquer l'accès
4. **Ajouter la méthode de réinscription** dans `PurchaseRepository.php`
5. **Créer l'interface admin** pour réinscrire un étudiant
6. **Tester** avec un étudiant réel

---

✅ **Voulez-vous que je commence l'implémentation ?**

