<?php

namespace App\Domain\Seo\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_name',
        'page_url',
        'page_title',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_active',
    ];

    protected $casts = [
        'meta_keywords' => 'array',
        'is_active' => 'boolean',
    ];
}
