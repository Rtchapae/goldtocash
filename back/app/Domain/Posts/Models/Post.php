<?php

namespace App\Domain\Posts\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';

    protected $fillable = [
        'user_id',
        'title',
        'body',
        'slug',
        'image',
        'active',
        'featured',
        'seo_title',
        'seo_description',
        'path_prefix',
    ];

    protected $casts = [
        'active' => 'boolean',
        'featured' => 'boolean',
    ];
}

