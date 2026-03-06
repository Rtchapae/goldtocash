<?php

namespace App\Domain\Orders\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class History extends Model
{
    protected $table = 'model_histories';

    public $timestamps = false;

    protected $fillable = [
        'model_type',
        'model_id',
        'user_id',
        'user_type',
        'message',
        'meta',
        'performed_at',
    ];

    protected $casts = [
        'performed_at' => 'datetime',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}


