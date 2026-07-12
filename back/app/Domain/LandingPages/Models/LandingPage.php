<?php

namespace App\Domain\LandingPages\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandingPage extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'path',
        'blocks',
        'seo_title',
        'seo_description',
        'active',
    ];

    protected $casts = [
        'blocks' => 'array',
        'active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
