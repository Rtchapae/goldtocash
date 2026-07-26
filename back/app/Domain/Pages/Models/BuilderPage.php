<?php

namespace App\Domain\Pages\Models;

use Illuminate\Database\Eloquent\Model;

class BuilderPage extends Model
{
    protected $table = 'builder_pages';

    protected $fillable = [
        'title',
        'slug',
        'seo_title',
        'seo_description',
        'blocks',
        'published',
        'created_by',
    ];

    protected $casts = [
        'blocks' => 'array',
        'published' => 'boolean',
    ];
}
