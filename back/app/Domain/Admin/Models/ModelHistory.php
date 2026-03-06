<?php

namespace App\Domain\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ModelHistory extends Model
{
    public $timestamps = false;

    public const USER_TYPE_SYSTEM = 'system';

    protected $table = 'model_histories';

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
        'meta' => 'array',
        'performed_at' => 'datetime',
    ];

    public function model(): MorphTo
    {
        return $this->morphTo()->constrain([
            'App\Domain\Orders\Models\Order' => function ($query) {
                $query->with(['user:id,first_name,last_name']);
            },
        ]);
    }

    public function user(): MorphTo
    {
        return $this->morphTo('user', 'user_type', 'user_id');
    }

    public function scopeOrderStatusChanges($query)
    {
        return $query->where('model_type', 'App\Domain\Orders\Models\Order')
                    ->where('message', 'like', '%status%');
    }
}
