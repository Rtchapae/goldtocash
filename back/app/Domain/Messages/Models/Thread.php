<?php

namespace App\Domain\Messages\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Thread extends Model
{
    use SoftDeletes;

    protected $table = 'threads';

    protected $fillable = [
        'subject',
        'slug',
        'max_participants',
        'start_date',
        'end_date',
        'avatar',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'thread_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(\App\Domain\Messages\Models\Participant::class, 'thread_id');
    }
}

