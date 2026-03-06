<?php

namespace App\Domain\Users\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trace extends Model
{
    protected $table = 'traces';

    protected $fillable = [
        'user_id',
        'hash',
        'source',
    ];

    protected $casts = [
        'source' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}





