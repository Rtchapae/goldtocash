<?php

namespace App\Domain\Sms\Models;

use Illuminate\Database\Eloquent\Model;

class SentMessageReceipt extends Model
{
    protected $table = 'sent_message_receipts';

    protected $fillable = [
        'mid',
        'status',
    ];
}

