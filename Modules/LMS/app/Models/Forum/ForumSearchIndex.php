<?php

namespace Modules\LMS\Models\Forum;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class ForumSearchIndex extends Model
{
    protected $table = 'forum_search_index';

    protected $fillable = [
        'searchable_id',
        'searchable_type',
        'keywords',
    ];

    // ============================================
    // RELATIONS
    // ============================================

    /**
     * Relation polymorphique vers ForumPost ou ForumPostReply
     */
    public function searchable(): MorphTo
    {
        return $this->morphTo();
    }

    // ============================================
    // MÉTHODES D'INDEXATION
    // ============================================

    /**
     * Indexer un post dans la recherche
     */
    public static function indexPost(ForumPost $post): self
    {
        $keywords = self::extractKeywords([
            $post->title,
            $post->description,
            $post->author->username ?? '',
            $post->tags->pluck('name')->implode(' '),
        ]);

        return self::updateOrCreate(
            [
                'searchable_type' => ForumPost::class,
                'searchable_id' => $post->id,
            ],
            [
                'keywords' => $keywords,
            ]
        );
    }

    /**
     * Indexer une réponse dans la recherche
     */
    public static function indexReply(ForumPostReply $reply): self
    {
        $keywords = self::extractKeywords([
            $reply->content,
            $reply->user->username ?? '',
            $reply->post->title ?? '',
        ]);

        return self::updateOrCreate(
            [
                'searchable_type' => ForumPostReply::class,
                'searchable_id' => $reply->id,
            ],
            [
                'keywords' => $keywords,
            ]
        );
    }

    /**
     * Extraire et nettoyer les mots-clés
     */
    protected static function extractKeywords(array $texts): string
    {
        $combined = implode(' ', $texts);

        // Nettoyer le HTML
        $combined = strip_tags($combined);

        // Convertir en minuscules
        $combined = Str::lower($combined);

        // Supprimer les caractères spéciaux (garder lettres, chiffres, espaces)
        $combined = preg_replace('/[^a-z0-9\s]/u', ' ', $combined);

        // Supprimer les mots courts (moins de 3 caractères)
        $words = explode(' ', $combined);
        $words = array_filter($words, function($word) {
            return strlen($word) >= 3;
        });

        // Supprimer les doublons
        $words = array_unique($words);

        return implode(' ', $words);
    }

    // ============================================
    // MÉTHODES DE RECHERCHE
    // ============================================

    /**
     * Rechercher dans l'index
     */
    public static function search(string $query, array $options = [])
    {
        $searchQuery = self::query();

        // Préparer les mots-clés de recherche
        $searchKeywords = self::extractKeywords([$query]);

        // Recherche full-text
        $searchQuery->whereRaw(
            "MATCH(keywords) AGAINST(? IN BOOLEAN MODE)",
            [$searchKeywords]
        );

        // Filtrer par type si spécifié
        if (isset($options['type'])) {
            $searchQuery->where('searchable_type', $options['type']);
        }

        // Filtrer par forum si spécifié
        if (isset($options['forum_id'])) {
            $searchQuery->whereHas('searchable', function($q) use ($options) {
                $q->where('forum_id', $options['forum_id']);
            });
        }

        return $searchQuery->with('searchable')
            ->orderByRaw("MATCH(keywords) AGAINST(? IN BOOLEAN MODE) DESC", [$searchKeywords])
            ->paginate($options['per_page'] ?? 20);
    }

    /**
     * Recherche simple (LIKE) pour bases de données sans full-text
     */
    public static function simpleSearch(string $query, array $options = [])
    {
        $searchQuery = self::query();

        // Recherche avec LIKE
        $keywords = explode(' ', trim($query));

        $searchQuery->where(function($q) use ($keywords) {
            foreach ($keywords as $keyword) {
                if (strlen($keyword) >= 3) {
                    $q->orWhere('keywords', 'LIKE', "%{$keyword}%");
                }
            }
        });

        // Filtres
        if (isset($options['type'])) {
            $searchQuery->where('searchable_type', $options['type']);
        }

        return $searchQuery->with('searchable')
            ->paginate($options['per_page'] ?? 20);
    }

    /**
     * Suggérer des résultats similaires
     */
    public static function suggest(string $query, int $limit = 5)
    {
        $searchKeywords = self::extractKeywords([$query]);

        return self::whereRaw(
            "MATCH(keywords) AGAINST(? IN BOOLEAN MODE)",
            [$searchKeywords]
        )
            ->with('searchable')
            ->limit($limit)
            ->get();
    }

    // ============================================
    // MÉTHODES DE MAINTENANCE
    // ============================================

    /**
     * Réindexer tous les posts
     */
    public static function reindexAllPosts(): int
    {
        $count = 0;

        ForumPost::chunk(100, function($posts) use (&$count) {
            foreach ($posts as $post) {
                self::indexPost($post);
                $count++;
            }
        });

        return $count;
    }

    /**
     * Réindexer toutes les réponses
     */
    public static function reindexAllReplies(): int
    {
        $count = 0;

        ForumPostReply::chunk(100, function($replies) use (&$count) {
            foreach ($replies as $reply) {
                self::indexReply($reply);
                $count++;
            }
        });

        return $count;
    }

    /**
     * Nettoyer les index orphelins
     */
    public static function cleanOrphans(): int
    {
        $deleted = 0;

        // Nettoyer les index de posts supprimés
        $postIndexes = self::where('searchable_type', ForumPost::class)->get();
        foreach ($postIndexes as $index) {
            if (!$index->searchable) {
                $index->delete();
                $deleted++;
            }
        }

        // Nettoyer les index de réponses supprimées
        $replyIndexes = self::where('searchable_type', ForumPostReply::class)->get();
        foreach ($replyIndexes as $index) {
            if (!$index->searchable) {
                $index->delete();
                $deleted++;
            }
        }

        return $deleted;
    }

    // ============================================
    // EVENTS
    // ============================================

    protected static function boot()
    {
        parent::boot();

        // Mettre à jour l'index quand un post est modifié
        ForumPost::updated(function ($post) {
            self::indexPost($post);
        });

        // Supprimer l'index quand un post est supprimé
        ForumPost::deleted(function ($post) {
            self::where('searchable_type', ForumPost::class)
                ->where('searchable_id', $post->id)
                ->delete();
        });

        // Mettre à jour l'index quand une réponse est modifiée
        ForumPostReply::updated(function ($reply) {
            self::indexReply($reply);
        });

        // Supprimer l'index quand une réponse est supprimée
        ForumPostReply::deleted(function ($reply) {
            self::where('searchable_type', ForumPostReply::class)
                ->where('searchable_id', $reply->id)
                ->delete();
        });
    }
}
