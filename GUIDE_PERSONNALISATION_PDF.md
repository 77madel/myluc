# 📘 GUIDE DE PERSONNALISATION DU CERTIFICAT PDF

## 📁 Fichier à modifier
**`Modules/LMS/app/Http/Controllers/CertificateControllerSimple.php`**  
Méthode: `downloadPdf()`

---

## 🎨 PARAMÈTRES À AJUSTER

### 📏 **Dimensions de l'image**
```php
$width = imagesx($image);   // Largeur totale: 2340px
$height = imagesy($image);  // Hauteur totale: 1654px
```

### 🎯 **POSITIONS DES TEXTES** (lignes 74-100)

#### **1. NOM DE L'ÉTUDIANT** (ligne 74-77)
```php
// Position actuelle
$x = ($width / 2) - (strlen($studentName) * 12);  // Centré horizontalement
$y = $height * 0.40;  // 40% depuis le haut
imagestring($image, 5, $x, $y, $studentName, $colorBlue);
```
**Ajustements possibles :**
- `$y = $height * 0.35` → Plus haut
- `$y = $height * 0.45` → Plus bas
- `imagestring($image, 5, ...)` → Taille 5 (la plus grande)

---

#### **2. TITRE DU COURS** (ligne 79-82)
```php
// Position actuelle
$x = ($width / 2) - (strlen($course_title) * 6);
$y = $height * 0.50;  // 50% depuis le haut
imagestring($image, 4, $x, $y, substr($course_title, 0, 60), $colorDarkBlue);
```
**Ajustements possibles :**
- `$y = $height * 0.48` → Plus haut
- `$y = $height * 0.52` → Plus bas
- `imagestring($image, 4, ...)` → Taille 4
- `substr($course_title, 0, 60)` → Limite à 60 caractères

---

#### **3. N° CERTIFICAT** (ligne 84-88)
```php
// Position actuelle
$certText = 'N° ' . $certificate->certificate_id;
$x = ($width / 2) - (strlen($certText) * 4);
$y = $height * 0.67;  // 67% depuis le haut
imagestring($image, 3, $x, $y, $certText, $colorBlack);
```
**Ajustements possibles :**
- `$y = $height * 0.65` → Plus haut
- `$y = $height * 0.70` → Plus bas
- `imagestring($image, 3, ...)` → Taille 3

---

#### **4. DATE** (ligne 90-94)
```php
// Position actuelle
$dateText = 'Fait à Bamako, le ' . $completion_date;
$x = $width * 0.20;  // 20% depuis la gauche
$y = $height * 0.90;  // 90% depuis le haut
imagestring($image, 3, $x, $y, $dateText, $colorGray);
```
**Ajustements possibles :**
- `$x = $width * 0.15` → Plus à gauche
- `$x = $width * 0.25` → Plus à droite
- `$y = $height * 0.85` → Plus haut
- `$y = $height * 0.92` → Plus bas

---

#### **5. INSTRUCTEUR** (ligne 96-100)
```php
// Position actuelle
$instructorText = 'Instructeur: ' . $instructor_name;
$x = $width * 0.50;  // 50% depuis la gauche
$y = $height * 0.82;  // 82% depuis le haut
imagestring($image, 3, $x, $y, $instructorText, $colorDark);
```
**Ajustements possibles :**
- `$x = $width * 0.45` → Plus à gauche
- `$x = $width * 0.55` → Plus à droite
- `$y = $height * 0.80` → Plus haut
- `$y = $height * 0.85` → Plus bas

---

## 🎨 **COULEURS DISPONIBLES**
```php
$colorBlue = imagecolorallocate($image, 26, 58, 82);      // Bleu foncé
$colorDarkBlue = imagecolorallocate($image, 44, 82, 130);  // Bleu moyen
$colorGray = imagecolorallocate($image, 107, 114, 128);    // Gris
$colorDark = imagecolorallocate($image, 44, 62, 80);       // Gris foncé
$colorBlack = imagecolorallocate($image, 0, 0, 0);         // Noir
```

**Pour ajouter une couleur :**
```php
$maNouvelleCouleur = imagecolorallocate($image, R, G, B);
// Exemple: Rouge
$colorRed = imagecolorallocate($image, 255, 0, 0);
```

---

## 📏 **TAILLES DE POLICE**
```php
imagestring($image, TAILLE, $x, $y, $texte, $couleur);
```
- **Taille 1** : Très petite
- **Taille 2** : Petite
- **Taille 3** : Moyenne (utilisée pour date, N°, instructeur)
- **Taille 4** : Grande (utilisée pour titre du cours)
- **Taille 5** : Très grande (utilisée pour nom étudiant)

---

## 🧮 **FORMULES DE CALCUL**

### Centrer un texte horizontalement :
```php
$x = ($width / 2) - (strlen($texte) * FACTEUR);
```
- **Taille 5** : FACTEUR ≈ 12
- **Taille 4** : FACTEUR ≈ 6
- **Taille 3** : FACTEUR ≈ 4

### Position verticale en pourcentage :
```php
$y = $height * POURCENTAGE;
```
- **0.00** = Tout en haut
- **0.50** = Au milieu
- **1.00** = Tout en bas

### Position horizontale en pourcentage :
```php
$x = $width * POURCENTAGE;
```
- **0.00** = Tout à gauche
- **0.50** = Au centre
- **1.00** = Tout à droite

---

## 💡 **EXEMPLES DE MODIFICATIONS**

### Déplacer le nom de l'étudiant plus haut :
```php
// Ligne 76 - Changer de 0.40 à 0.35
$y = $height * 0.35;  // Au lieu de 0.40
```

### Déplacer la date à droite :
```php
// Ligne 92 - Changer de 0.20 à 0.70
$x = $width * 0.70;  // Au lieu de 0.20
```

### Changer la couleur du nom :
```php
// Ligne 77 - Utiliser colorDarkBlue au lieu de colorBlue
imagestring($image, 5, $x, $y, $studentName, $colorDarkBlue);
```

---

## 🔄 **APRÈS MODIFICATION**

1. Sauvegardez le fichier
2. Videz le cache : `php artisan view:clear`
3. Testez en téléchargeant un certificat

---

## 📍 **COORDONNÉES ACTUELLES**

| Élément | X (horizontal) | Y (vertical) | Taille | Couleur |
|---------|----------------|--------------|---------|---------|
| Nom étudiant | Centre | 40% | 5 | Bleu |
| Titre cours | Centre | 50% | 4 | Bleu foncé |
| N° Certificat | Centre | 67% | 3 | Noir |
| Date | 20% gauche | 90% | 3 | Gris |
| Instructeur | 50% centre | 82% | 3 | Gris foncé |

---

🎯 **Modifiez les valeurs dans le contrôleur pour ajuster les positions !**

