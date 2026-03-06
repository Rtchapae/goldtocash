<?php

namespace App\Domain\Admin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Users\Models\User::class, 'admin_user_id');
    }

    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function isRead(): bool
    {
        return $this->is_read;
    }

    public static function getTypes(): array
    {
        return [
            'appraisal_request' => 'Appraisal Request',
            'offer_response' => 'Offer Response',
            'status_change' => 'Status Change',
            'offline_transaction' => 'Offline Transaction',
        ];
    }
}




