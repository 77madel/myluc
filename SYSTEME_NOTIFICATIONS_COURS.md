# 🔔 SYSTÈME DE NOTIFICATIONS - Complétion de Cours

## ✅ SYSTÈME EXISTANT

### **Notification de Certificat**

**Fichier :** `app/Services/CertificateService.php` (lignes 174, 243-256)

**Quand est-elle envoyée ?**
- ✅ Automatiquement quand un **certificat est généré**
- ✅ Après que l'étudiant ait terminé **tous les topics** du cours

**Code :**
```php
// Ligne 174 - Appelé après la génération du certificat
self::sendCertificateNotification($user, $course, $userCertificate);

// Ligne 243 - Méthode d'envoi
public static function sendCertificateNotification($user, $course, $userCertificate): void
{
    try {
        Log::info("🔔 Envoi de notification de certificat à l'utilisateur {$user->id}");

        // Envoyer la notification via le système Laravel
        $user->notify(new \Modules\LMS\Notifications\NotifyCertificateGenerated(
            $userCertificate, 
            $course, 
            $user
        ));

        Log::info("✅ Notification certificat envoyée avec succès");

    } catch (\Exception $e) {
        Log::error("❌ Erreur envoi notification certificat: " . $e->getMessage());
    }
}
```

---

## 📧 CLASSE DE NOTIFICATION

**Fichier :** `app/Notifications/NotifyCertificateGenerated.php`

**Canal utilisé :**
```php
public function via($notifiable): array
{
    return ['database'];  // Notification stockée dans la base de données
}
```

**Contenu de la notification :**
```php
public function toArray($notifiable): array
{
    return [
        'type' => 'certificate_generated',
        'title' => '🎓 Félicitations ! Votre certificat est prêt',
        'message' => "Félicitations ! Vous avez terminé le cours \"{$this->course->title}\" 
                      et votre certificat est disponible.",
        'certificate_id' => $this->certificate->id,
        'course_title' => $this->course->title,
        'certificate_date' => $this->certificate->certificated_date->format('d/m/Y'),
        'action_url' => route('student.certificate.download', $this->certificate->id),
        'icon' => '🎓'
    ];
}
```

---

## 📍 OÙ L'ÉTUDIANT VOIT LA NOTIFICATION

### **1. Icône de notification dans le header**

**Fichier :** `resources/views/portals/components/admin/header.blade.php` (lignes 86-90)

```blade
@if ($isStudent)
    <x-portal::admin.notification 
        read-route="{{ route('student.notification.read.all') }}"
        route="{{ route('student.notification.history') }}" 
        :notifications="Auth::user()->unreadNotifications"
        singleRoute="student.notification.history.status" />
@endif
```

**Affichage :**
- 🔔 **Icône de cloche** avec un badge rouge
- **Nombre de notifications** non lues
- **Dropdown** au clic avec la liste

---

### **2. Page historique des notifications**

**Route :** `/student/notification/history`  
**Fichier :** `resources/views/portals/student/notification/index.blade.php`

**Affichage :**
- **Tableau** avec toutes les notifications
- **Colonnes :**
  - Titre : "🎓 Félicitations ! Votre certificat est prêt"
  - Message : "Félicitations ! Vous avez terminé le cours..."
  - Date : "Il y a 2 heures"
  - Status : Lu/Non lu (toggle)
  - Action : Bouton supprimer

---

## 🔄 WORKFLOW COMPLET

```
┌─────────────────────────────────────┐
│ Étudiant termine le dernier topic   │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│ CertificateService génère certificat│
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│ sendCertificateNotification()       │
│ → Notification envoyée              │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│ Stockée dans table "notifications"  │
│ → user_id, type, data (JSON)        │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│ Étudiant voit dans le header :      │
│ 🔔 (1) ← Badge rouge                │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│ Clic sur 🔔 → Dropdown s'ouvre     │
│ "🎓 Votre certificat est prêt"      │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│ Clic sur notification →             │
│ Redirection vers certificat         │
└─────────────────────────────────────┘
```

---

## 📊 TABLE `notifications`

**Structure :**
```sql
CREATE TABLE notifications (
    id UUID PRIMARY KEY,
    type VARCHAR,              -- Classe de notification
    notifiable_type VARCHAR,   -- App\Models\User
    notifiable_id BIGINT,      -- ID de l'utilisateur
    data JSON,                 -- Contenu de la notification
    read_at TIMESTAMP,         -- NULL = non lu, rempli = lu
    created_at TIMESTAMP
);
```

**Exemple de data (JSON) :**
```json
{
    "type": "certificate_generated",
    "title": "🎓 Félicitations ! Votre certificat est prêt",
    "message": "Félicitations ! Vous avez terminé le cours \"Marketing Digital\" et votre certificat est disponible.",
    "certificate_id": 123,
    "course_title": "Marketing Digital Complet",
    "certificate_date": "25/10/2025",
    "action_url": "/student/certificate/download/123",
    "icon": "🎓"
}
```

---

## 🔍 VÉRIFIER LES NOTIFICATIONS

### **Via Eloquent :**

```php
use Modules\LMS\Models\User;

$user = User::find(53); // Famory

// Toutes les notifications
$allNotifications = $user->notifications;

// Notifications non lues
$unread = $user->unreadNotifications;

// Notifications de certificat
$certificateNotifs = $user->notifications()
    ->whereJsonContains('data->type', 'certificate_generated')
    ->get();

foreach ($certificateNotifs as $notif) {
    echo "🎓 " . $notif->data['title'] . "\n";
    echo "   Cours : " . $notif->data['course_title'] . "\n";
    echo "   Date : " . $notif->created_at->format('d/m/Y H:i') . "\n";
    echo "   Lu : " . ($notif->read_at ? 'Oui' : 'Non') . "\n";
    echo "\n";
}
```

### **Via SQL :**

```sql
-- Notifications de certificat pour l'utilisateur 53
SELECT 
    id,
    JSON_EXTRACT(data, '$.title') as title,
    JSON_EXTRACT(data, '$.course_title') as course,
    read_at,
    created_at
FROM notifications
WHERE notifiable_id = 53
AND notifiable_type = 'Modules\\LMS\\Models\\User'
AND JSON_EXTRACT(data, '$.type') = 'certificate_generated'
ORDER BY created_at DESC;
```

---

## ✅ RÉSUMÉ

**Le système de notification fonctionne déjà :**

1. ✅ **Notification créée** automatiquement après certificat
2. ✅ **Stockée** dans la table `notifications`
3. ✅ **Affichée** dans l'icône 🔔 du header
4. ✅ **Accessible** via `/student/notification/history`
5. ✅ **Bouton d'action** pour télécharger le certificat

**Canal :**
- ✅ `database` (stocké dans la base)
- ❌ Pas d'email (pas configuré)
- ❌ Pas de push notification (pas configuré)

**Pour activer l'email :**
```php
// Modifier NotifyCertificateGenerated.php
public function via($notifiable): array
{
    return ['database', 'mail'];  // Ajouter 'mail'
}

public function toMail($notifiable)
{
    return (new MailMessage)
        ->subject('🎓 Votre certificat est prêt !')
        ->line('Félicitations ! Vous avez terminé le cours "' . $this->course->title . '"')
        ->action('Télécharger mon certificat', route('student.certificate.download', $this->certificate->id));
}
```

---

**🎯 Le système de notification fonctionne déjà ! L'étudiant peut voir ses notifications dans le header (🔔) et dans la page d'historique !** 📧

