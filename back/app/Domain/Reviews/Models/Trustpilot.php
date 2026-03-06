<?php

namespace App\Domain\Reviews\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trustpilot extends Model
{
    use HasFactory;

    protected $table = 'trustpilot';

    protected $fillable = [
        'trustpilot_id',
        'author_name',
        'author_type',
        'author_url',
        'title',
        'review',
        'rating',
        'date_published',
    ];

    protected $casts = [
        'date_published' => 'datetime',
        'rating' => 'integer',
    ];
}


