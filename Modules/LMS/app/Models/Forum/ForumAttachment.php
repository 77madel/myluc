<?php

// Modules/LMS/Models/Forum/ForumAttachment.php

namespace Modules\LMS\Models\Forum;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class ForumAttachment extends Model
{
    protected $fillable = [
        'attachable_id',
        'attachable_type',
        'file_name',
        'file_path',
        'file_type',
        'mime_type',
        'file_size',
        'url',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    // Relations
    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    // Méthodes utilitaires
    public function getUrl(): string
    {
        if ($this->file_type === 'link') {
            return $this->url;
        }

        return Storage::url($this->file_path);
    }

    public function download()
    {
        return Storage::download($this->file_path, $this->file_name);
    }

    public function deleteFile(): void
    {
        if ($this->file_path && Storage::exists($this->file_path)) {
            Storage::delete($this->file_path);
        }
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($attachment) {
            $attachment->deleteFile();
        });
    }
}
