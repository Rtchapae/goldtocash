<?php

namespace App\Domain\Messages\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domain\Users\Models\User;

class Participant extends Model
{
    use SoftDeletes;

    protected $table = 'participants';

    protected $fillable = [
        'thread_id',
        'user_id',
        'last_read',
        'starred',
    ];

    protected $casts = [
        'last_read' => 'datetime',
        'starred' => 'boolean',
    ];

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class, 'thread_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}


