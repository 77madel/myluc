<?php

namespace Modules\LMS\Models\Certificate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'certificate_content',
        'input_content',
        'type',
        'status'
    ];

    protected $casts = [
        'input_content' => 'array',
    ];
}
