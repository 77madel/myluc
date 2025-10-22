# 🗄️ Structure de la Base de Données LMS

## 📊 Vue d'ensemble de l'architecture

Votre système LMS utilise une architecture modulaire avec des relations polymorphes pour gérer différents types de contenu pédagogique.

## 🏗️ Tables Principales

### 1. **COURSES** (Cours)
```sql
- id (PK)
- title (string)
- slug (string)
- organization_id (FK)
- admin_id (FK)
- category_id (FK)
- subject_id (FK)
- thumbnail (string)
- video_src_type (string) -- 'youtube', 'vimeo', 'local'
- short_video (string)
- demo_url (string)
- short_description (text)
- description (longText)
- duration (string)
- status (enum: 'Pending', 'Rejected', 'Approved')
- timestamps
- soft_deletes
```

### 2. **CHAPTERS** (Chapitres)
```sql
- id (PK)
- course_id (FK → courses.id)
- title (string)
- order (string)
- timestamps
- soft_deletes
```

### 3. **TOPICS** (Leçons/Sujets) - Table Polymorphe
```sql
- id (PK)
- chapter_id (FK → chapters.id)
- course_id (FK → courses.id)
- topicable_id (int) -- ID du contenu spécifique
- topicable_type (string) -- Type du contenu (Video, Reading, etc.)
- order (int)
- timestamps
```

## 🎯 Types de Contenu (Topicable)

### 4. **VIDEOS** (Vidéos)
```sql
- id (PK)
- topic_type_id (FK → topic_types.id)
- title (string)
- duration (string)
- video_src_type (string) -- 'youtube', 'vimeo', 'local'
- video_url (string) -- URL YouTube/Vimeo
- system_video (string) -- Fichier vidéo local
- timestamps
```

### 5. **READINGS** (Lectures)
```sql
- id (PK)
- topic_type_id (FK → topic_types.id)
- title (string)
- description (text)
- timestamps
```

### 6. **ASSIGNMENTS** (Devoirs)
```sql
- id (PK)
- topic_type_id (FK → topic_types.id)
- title (string)
- duration (string)
- description (text)
- total_mark (int)
- pass_mark (int)
- retake_number (int)
- submission_date (timestamp)
- timestamps
```

### 7. **QUIZZES** (Quiz)
```sql
- id (PK)
- instructor_id (FK → instructors.id)
- topic_id (FK → topics.id)
- topic_type_id (FK → topic_types.id)
- quiz_type_id (FK → quiz_types.id)
- title (string)
- duration (string)
- total_mark (int)
- pass_mark (int)
- total_retake (int)
- instruction (text)
- is_random_question (int)
- is_certificate (int)
- expire_date (timestamp)
- status (int)
- timestamps
```

### 8. **SUPPLEMENTS** (Suppléments)
```sql
- id (PK)
- topic_type_id (FK → topic_types.id)
- title (string)
- duration (string)
- description (longText)
- timestamps
```

### 9. **LECTURES** (Conférences)
```sql
- id (PK)
- topic_type_id (FK → topic_types.id)
- title (string)
- duration (string)
- timestamps
```

## 📈 Système de Progression

### 10. **TOPIC_PROGRESS** (Progression des Leçons)
```sql
- id (PK)
- user_id (FK → users.id)
- topic_id (FK → topics.id)
- chapter_id (FK → chapters.id)
- course_id (FK → courses.id)
- status (enum: 'not_started', 'in_progress', 'completed')
- started_at (timestamp)
- completed_at (timestamp)
- time_spent (int) -- en secondes
- timestamps
- UNIQUE(user_id, topic_id)
```

### 11. **CHAPTER_PROGRESS** (Progression des Chapitres)
```sql
- id (PK)
- user_id (FK → users.id)
- chapter_id (FK → chapters.id)
- course_id (FK → courses.id)
- status (enum: 'not_started', 'in_progress', 'completed')
- started_at (timestamp)
- completed_at (timestamp)
- time_spent (int) -- en secondes
- timestamps
- UNIQUE(user_id, chapter_id)
```

## 🎓 Inscription et Accès

### 12. **ENROLLMENTS** (Inscriptions)
```sql
- id (PK)
- student_id (FK → users.id)
- organization_id (FK → users.id) -- nullable
- course_id (FK → courses.id)
- course_title (string)
- price (decimal)
- status (enum: 'free', 'paid')
- course_status (enum: 'processing', 'completed')
- timestamps
```

## ⚙️ Configuration des Cours

### 13. **COURSE_SETTINGS** (Paramètres des Cours)
```sql
- id (PK)
- course_id (FK → courses.id)
- access_days (int)
- sale_count_number (int)
- seat_capacity (int)
- has_support (int)
- is_certificate (int) -- 0/1
- is_downloadable (int)
- has_course_forum (int)
- has_subscription (int)
- is_wait_list (int)
- is_free (int)
- is_live (int)
- is_upcoming (int)
- timestamps
- soft_deletes
```

## 🔗 Relations Clés

### Relations Principales :
1. **Course** → **Chapters** (1:N)
2. **Chapter** → **Topics** (1:N)
3. **Topic** → **Topicable** (1:1 polymorphe)
4. **User** → **Topic_Progress** (1:N)
5. **User** → **Chapter_Progress** (1:N)
6. **User** → **Enrollments** (1:N)

### Relations Polymorphes :
- **Topic.topicable** → **Video/Reading/Assignment/Quiz/Supplement/Lecture**

## 🎯 Flux de Données pour la Progression

### 1. **Inscription à un cours**
```
User → Enrollments → Course
```

### 2. **Progression d'une leçon**
```
User → Topic_Progress → Topic → Topicable (Video/Reading/etc.)
```

### 3. **Progression d'un chapitre**
```
User → Chapter_Progress → Chapter
```

### 4. **Vérification de completion**
```
Tous les Topics d'un Chapter sont completed → Chapter completed
```

## 🚀 Optimisations et Index

### Index de Performance :
- `topic_progress`: `(user_id, topic_id)`, `(user_id, chapter_id)`, `(user_id, course_id)`
- `chapter_progress`: `(user_id, course_id)`, `(user_id, chapter_id)`, `(course_id, status)`
- `enrollments`: `(student_id, course_id)`

### Contraintes Uniques :
- `topic_progress`: `(user_id, topic_id)`
- `chapter_progress`: `(user_id, chapter_id)`
- `enrollments`: `(student_id, course_id)`

## 📊 Types de Contenu Supportés

1. **VIDEO** - Vidéos (YouTube, Vimeo, Local)
2. **READING** - Lectures/Textes
3. **ASSIGNMENT** - Devoirs
4. **QUIZ** - Quiz/Examens
5. **SUPPLEMENT** - Suppléments
6. **LECTURE** - Conférences

## 🎯 Système de Progression Automatique

### Déclencheurs :
- **Fin de vidéo** → `topic_progress.completed_at = now()`
- **Toutes les leçons terminées** → `chapter_progress.completed_at = now()`

### Métriques :
- **Temps passé** : `time_spent` en secondes
- **Statut** : `not_started`, `in_progress`, `completed`
- **Dates** : `started_at`, `completed_at`

---

## 🎉 Conclusion

Cette architecture permet une gestion flexible et scalable des cours en ligne avec :
- ✅ **Contenu polymorphe** (vidéos, lectures, quiz, etc.)
- ✅ **Progression granulaire** (leçon → chapitre → cours)
- ✅ **Suivi détaillé** (temps, dates, statuts)
- ✅ **Certification** (paramètres configurables)
- ✅ **Organisations** (support multi-tenant)

Le système est **prêt pour la production** et supporte tous les cas d'usage d'un LMS moderne ! 🚀

