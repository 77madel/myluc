# 📚 Cours d'Organisation avec Chapitres et Leçons

## 🎯 Résumé des Cours Créés

### **Cours 1 : Introduction à React en 2 minutes**
- **Prix** : 29.99€
- **Durée** : 2 minutes
- **Type** : Vidéo YouTube courte
- **Organisation** : Tech Academy
- **Instructeur** : Jean Dupont
- **Certification** : ✅ Activée

#### 📖 Structure du Cours :
**Chapitre 1 : Introduction à React**
- Leçon 1 : "Qu'est-ce que React ?" (Vidéo YouTube - 2 minutes)
- Leçon 2 : Lecture complémentaire

**Chapitre 2 : Concepts de base**
- Leçon 1 : "Composants et JSX" (Vidéo YouTube - 1.5 minutes)

---

### **Cours 2 : CSS Grid en 90 secondes**
- **Prix** : 19.99€
- **Durée** : 90 secondes
- **Type** : Vidéo YouTube courte
- **Organisation** : Tech Academy
- **Instructeur** : Jean Dupont
- **Certification** : ✅ Activée

#### 📖 Structure du Cours :
**Chapitre 1 : Introduction à CSS Grid**
- Leçon 1 : "Définir une grille CSS" (Vidéo YouTube - 90 secondes)
- Leçon 2 : Lecture complémentaire

**Chapitre 2 : Positionnement des éléments**
- Leçon 1 : "Grid areas et placement" (Vidéo YouTube - 1 minute)

---

## 🏢 Données Créées

### **Organisation**
- **Nom** : Tech Academy
- **Email** : organization@example.com
- **Type** : Organisation

### **Instructeur**
- **Nom** : Jean Dupont
- **Email** : instructor@example.com
- **Type** : Instructeur

### **Catégorie**
- **Nom** : Technologie & Innovation
- **Sujet** : Programmation Web
- **Langue** : Français
- **Niveau** : Débutant

---

## 🚀 Comment Exécuter le Seeder

### **Option 1 : Via Artisan (Recommandé)**
```bash
cd Modules/LMS
php artisan db:seed --class="Modules\LMS\Database\Seeders\OrganizationCoursesSeeder"
```

### **Option 2 : Via le script PHP**
```bash
php run_organization_courses_seeder.php
```

### **Option 3 : Via le seeder principal**
```bash
php artisan db:seed
```

---

## 📋 Fonctionnalités Incluses

### ✅ **Cours Payants**
- Prix configurés (29.99€ et 19.99€)
- Frais de plateforme (2.99€)
- Devise : EUR

### ✅ **Vidéos YouTube Courtes**
- Durée : 1-2 minutes maximum
- Source : YouTube
- URLs réelles de vidéos courtes

### ✅ **Structure Pédagogique**
- Chapitres organisés
- Leçons avec vidéos et lectures
- Ordre séquentiel

### ✅ **Certification**
- Certificats activés pour les deux cours
- Génération automatique à la fin du cours

### ✅ **Organisation Complète**
- Organisation avec profil utilisateur
- Instructeur assigné
- Relations configurées

---

## 🎥 Vidéos YouTube Utilisées

### **React**
- URL : `https://www.youtube.com/watch?v=Ke90Tje7VS0`
- Durée : 2 minutes
- Contenu : Introduction à React

### **CSS Grid**
- URL : `https://www.youtube.com/watch?v=0xMQfnTU6oE`
- Durée : 90 secondes
- Contenu : CSS Grid basics

---

## 📊 Base de Données

### **Tables Créées/Modifiées**
- `courses` - Cours principaux
- `course_prices` - Prix des cours
- `course_settings` - Paramètres (certification, etc.)
- `chapters` - Chapitres des cours
- `topics` - Leçons/leçons
- `videos` - Vidéos YouTube
- `lectures` - Lectures complémentaires
- `users` - Organisation et instructeur
- `course_instructors` - Relations cours-instructeurs
- `course_languages` - Langues des cours
- `course_levels` - Niveaux des cours

---

## 🎯 Résultat Final

Le seeder crée un environnement complet avec :
- **2 cours payants** avec vidéos YouTube courtes
- **Structure pédagogique** complète (chapitres + leçons)
- **Organisation** et instructeur configurés
- **Certification** activée
- **Prix** configurés
- **Relations** entre toutes les entités

Parfait pour tester le système LMS avec des cours réalistes ! 🚀



