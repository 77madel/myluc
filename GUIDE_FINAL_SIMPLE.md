# 🎯 GUIDE FINAL - MODIFICATION DU PDF

## ✅ SOLUTION ACTUELLE

L'image est maintenant **800×600px** (identique à votre HTML) !  
**Les positions sont exactement les mêmes** qu'en HTML !  
**Le titre complet s'affiche sur 2 lignes si nécessaire** (centré) !

---

## 📝 FICHIER À MODIFIER

**`Modules/LMS/app/Http/Controllers/CertificateControllerSimple.php`**

**Lignes 112-154** contiennent TOUT le positionnement !

---

## 📏 AUGMENTER LA TAILLE (très simple)

Les tailles vont de **1 à 5** (5 = le plus grand)

```php
// LIGNE 115 - Nom étudiant
imagestring($image, 5, $x, $y, utf8_decode($studentName), $colorBlue);
                    ↑ Taille (1-5) - 5 est déjà le maximum !

// LIGNES 141, 147 - Titre formation (2 lignes)
imagestring($image, 4, $x, $y, $line1, $colorDarkBlue);
                    ↑ Changez en 5 pour agrandir

// LIGNE 159 - N° Certificat
imagestring($image, 4, $x, $y, $certificate->certificate_id, $colorBlack);
                    ↑ Taille 4 (changez en 5 si trop petit)

// LIGNE 166 - Date  
imagestring($image, 4, $x, $y, utf8_decode($dateText), $colorPurple);
                    ↑ Taille 4 (changez en 5 si trop petit)

// LIGNE 171 - Instructeur
imagestring($image, 4, $x, $y, utf8_decode($instructor_name), $colorBlack);
                    ↑ Taille 4 (changez en 5 si trop petit)
```

---

## 🔤 TITRE SUR PLUSIEURS LIGNES

**Configuration automatique** (ligne 118) :
```php
$maxCharsPerLine = 45;  // Changez cette valeur pour contrôler la longueur
```

- **Plus grand** (ex: 60) = moins de lignes, titres plus longs sur une ligne
- **Plus petit** (ex: 30) = plus de lignes, découpage plus fréquent

**Positions des 2 lignes** :
```php
// Ligne 140 - Première ligne du titre
$y = 290;  // Position verticale
           // Changez en 280 pour monter, 300 pour descendre

// Ligne 146 - Deuxième ligne du titre
$y = 310;  // Position verticale (20px en dessous de la première)
           // Changez en 320 pour plus d'espace entre les lignes
```

---

## ⬅️➡️ DÉPLACER GAUCHE/DROITE

```php
// LIGNE 113 - Nom (centré à 400px = 50% de 800)
$x = 400 - (strlen($studentName) * 4);
     ↑ Changez 400 en:
     - 300 pour déplacer à GAUCHE
     - 500 pour déplacer à DROITE

// LIGNES 139, 145 - Titre (centré à 400px)
$x = 400 - (strlen($line1) * 3);
     ↑ Changez 400 en:
     - 300 pour déplacer à GAUCHE
     - 500 pour déplacer à DROITE

// LIGNE 164 - Date (à 480px = 60% de 800)  
$x = 480;
     ↑ Changez en:
     - 400 pour déplacer à GAUCHE
     - 550 pour déplacer à DROITE

// LIGNE 170 - Instructeur (à 423px)
$x = 423;
     ↑ Changez en:
     - 350 pour déplacer à GAUCHE
     - 500 pour déplacer à DROITE
```

---

## ⬆️⬇️ DÉPLACER HAUT/BAS

```php
// LIGNE 114 - Nom (à 240px = 40% de 600)
$y = 240;
     ↑ Changez en:
     - 200 pour MONTER
     - 280 pour DESCENDRE

// LIGNES 140, 146 - Titre (à 290px et 310px)
$y = 290;  // Première ligne
     ↑ Changez en:
     - 270 pour MONTER
     - 310 pour DESCENDRE

$y = 310;  // Deuxième ligne
     ↑ Changez en:
     - 290 pour MONTER
     - 330 pour DESCENDRE

// LIGNE 165 - Date (à 402px = 67% de 600)
$y = 402;
     ↑ Changez en:
     - 380 pour MONTER
     - 450 pour DESCENDRE
```

---

## 🔤 PROBLÈME UTF-8 (Caractères français)

`imagestring()` **ne supporte PAS parfaitement** les caractères accentués !

**Solution temporaire :** `utf8_decode()` est déjà appliqué
```php
// Ligne 115, 166, 171 - Déjà corrigé avec utf8_decode()
utf8_decode($studentName)
```

**Note :** La date est affichée "Fait a Bamako" (sans accent sur le à)

---

## 🎨 CORRESPONDANCE HTML/PDF

| Élément | Position HTML | Position PDF (800×600) |
|---------|---------------|------------------------|
| Nom | `left: 50%, top: 40%` | `$x=400, $y=240` |
| Titre (1 ligne) | `left: 50%, top: 50%` | `$x=400, $y=300` |
| Titre (2 lignes) | `left: 50%, top: 48.3%` | `$x=400, $y=290` |
|                  | `left: 50%, top: 51.7%` | `$x=400, $y=310` |
| N° | `left: 525px, top: 524px` | `$x=525, $y=524` |
| Date | `left: 60%, bottom: 33%` | `$x=480, $y=402` |
| Instructeur | `right: 377px, bottom: 29%` | `$x=423, $y=426` |

---

## 🚀 APRÈS MODIFICATION

```bash
php artisan view:clear
php artisan cache:clear
```

Puis téléchargez un nouveau certificat !

---

✅ **Le titre complet s'affiche maintenant sur plusieurs lignes centrées !**
