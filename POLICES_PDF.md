# 🎨 POLICES PERSONNALISÉES DANS LE PDF

## ✅ SYSTÈME DE FALLBACK IMPLÉMENTÉ

Le PDF essaie maintenant plusieurs polices dans cet ordre :
1. **Segoe UI** (Windows) - `C:/Windows/Fonts/segoeui.ttf`
2. **Trebuchet MS** (Windows) - `C:/Windows/Fonts/trebuc.ttf`
3. **DejaVu Sans** (Linux) - `/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf`
4. **DejaVu Sans** (TCPDF) - Dans le dossier vendor

**La première police trouvée sera utilisée !**

---

## 🔧 COMMENT ÇA MARCHE

### **Si une police TTF est trouvée** :
✅ Utilise `imagettftext()` avec **tailles personnalisées** (en points)
✅ Supporte les **accents** et caractères spéciaux
✅ **Meilleure qualité** de rendu

### **Si aucune police TTF n'est trouvée** :
⚠️ Utilise `imagestring()` avec **tailles limitées** (1-5)
⚠️ Utilise `utf8_decode()` pour les accents
⚠️ Qualité standard

---

## 📏 TAILLES DE POLICE (avec TTF)

Dans `CertificateControllerSimple.php` :

```php
// LIGNE 142 - Nom étudiant
$fontSize = 22;  // Changez ce chiffre (10-40)

// LIGNE 152 - Titre formation
$titleFontSize = 18;  // Changez ce chiffre (10-30)

// LIGNE 196 - N° Certificat
$certFontSize = 11;  // Changez ce chiffre (8-16)

// LIGNE 205 - Date
$dateFontSize = 14;  // Changez ce chiffre (10-18)

// LIGNE 211 - Instructeur
$instructorFontSize = 14;  // Changez ce chiffre (10-18)
```

---

## ➕ AJOUTER D'AUTRES POLICES

Pour ajouter une police, modifiez **ligne 109-114** :

```php
$fontOptions = [
    'C:/Windows/Fonts/segoeui.ttf',      // Segoe UI
    'C:/Windows/Fonts/trebuc.ttf',       // Trebuchet MS
    'C:/Windows/Fonts/arial.ttf',        // Arial (AJOUTÉ)
    'C:/Windows/Fonts/georgia.ttf',      // Georgia (AJOUTÉ)
    '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
    dirname(__DIR__, 3) . '/../../vendor/tecnickcom/tcpdf/fonts/dejavusans.ttf',
];
```

---

## 🎯 VÉRIFIER QUELLE POLICE EST UTILISÉE

Le système log un warning si aucune police TTF n'est trouvée.

Vérifiez dans `storage/logs/laravel.log` :
```
[WARNING] Aucune police TTF trouvée, utilisation de imagestring()
```

---

## 💡 AVANTAGES DU NOUVEAU SYSTÈME

| Avant | Maintenant |
|-------|------------|
| ❌ Tailles limitées (1-5) | ✅ Tailles personnalisées (10-100) |
| ❌ Pas d'accents | ✅ Accents supportés |
| ❌ Police système uniquement | ✅ Segoe UI, Trebuchet MS, etc. |
| ❌ Qualité moyenne | ✅ Haute qualité |

---

✅ **Le PDF utilise maintenant des polices modernes comme votre HTML !**

