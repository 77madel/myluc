<?php

// ============================================
// MODÈLE 1 : ForumTag
// ============================================
// Fichier: Modules/LMS/Models/Forum/ForumTag.php

namespace Modules\LMS\Models\Forum;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class ForumTag extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'color',
        'usage_count',
    ];

    protected $casts = [
        'usage_count' => 'integer',
    ];

    // ============================================
    // RELATIONS
    // ============================================

    /**
     * Les posts associés à ce tag
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(
            ForumPost::class,
            'forum_post_tags',
            'forum_tag_id',
            'forum_post_id'
        )->withTimestamps();
    }

    // ============================================
    // MÉTHODES UTILITAIRES
    // ============================================

    /**
     * Incrémenter le compteur d'utilisation
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    /**
     * Décrémenter le compteur d'utilisation
     */
    public function decrementUsage(): void
    {
        if ($this->usage_count > 0) {
            $this->decrement('usage_count');
        }
    }

    /**
     * Créer ou récupérer un tag par son nom
     */
    public static function findOrCreateByName(string $name): self
    {
        $slug = Str::slug($name);

        $tag = self::where('slug', $slug)->first();

        if (!$tag) {
            $tag = self::create([
                'name' => $name,
                'slug' => $slug,
                'color' => self::generateRandomColor(),
            ]);
        }

        return $tag;
    }

    /**
     * Créer plusieurs tags à partir d'un tableau de noms
     */
    public static function createMultiple(array $tagNames): array
    {
        $tags = [];

        foreach ($tagNames as $name) {
            $name = trim($name);
            if (!empty($name)) {
                $tags[] = self::findOrCreateByName($name);
            }
        }

        return $tags;
    }

    /**
     * Récupérer les tags les plus utilisés
     */
    public static function getMostUsed(int $limit = 10)
    {
        return self::orderByDesc('usage_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Rechercher des tags par nom
     */
    public static function search(string $query, int $limit = 10)
    {
        return self::where('name', 'LIKE', "%{$query}%")
            ->orWhere('slug', 'LIKE', "%{$query}%")
            ->orderByDesc('usage_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Générer une couleur aléatoire pour le tag
     */
    protected static function generateRandomColor(): string
    {
        $colors = [
            '#3B82F6', // Blue
            '#EF4444', // Red
            '#10B981', // Green
            '#F59E0B', // Yellow
            '#8B5CF6', // Purple
            '#EC4899', // Pink
            '#06B6D4', // Cyan
            '#F97316', // Orange
            '#14B8A6', // Teal
            '#6366F1', // Indigo
        ];

        return $colors[array_rand($colors)];
    }

    /**
     * Nettoyer les tags inutilisés
     */
    public static function cleanUnused(): int
    {
        return self::where('usage_count', 0)->delete();
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Scope pour les tags actifs (utilisés)
     */
    public function scopeActive($query)
    {
        return $query->where('usage_count', '>', 0);
    }

    /**
     * Scope pour trier par popularité
     */
    public function scopePopular($query)
    {
        return $query->orderByDesc('usage_count');
    }

    // ============================================
    // EVENTS
    // ============================================

    protected static function boot()
    {
        parent::boot();

        // Générer le slug automatiquement avant la création
        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }

            if (empty($tag->color)) {
                $tag->color = self::generateRandomColor();
            }
        });
    }
}
