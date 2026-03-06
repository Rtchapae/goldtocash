<?php

namespace App\Domain\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    protected $table = 'statuses';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'value',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'status');
    }
}





