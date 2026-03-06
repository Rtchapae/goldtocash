<?php

namespace App\Domain\Orders\Models;

use App\Domain\Users\Models\Branch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'description',
        'notes',
        'amount',
        'status',
        'order_type',
        'branch_id',
        'payment_method',
        'welcome',
        'send_label',
        'shipping',
        'submission_url',
        'account_confirmed',
        'account_confirmed_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Users\Models\User::class);
    }

    public function statusRelation(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}


