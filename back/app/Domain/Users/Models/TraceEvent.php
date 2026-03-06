<?php

namespace App\Domain\Users\Models;

use Illuminate\Database\Eloquent\Model;

class TraceEvent extends Model
{
    public const EVENT_TRACE_STARTED = 'trace-started';
    public const EVENT_PAGE_LOAD = 'page-load';
    public const EVENT_KIT_REQUEST = 'kit-request';
    public const EVENT_ITEMS_RECEIVED = 'items-received';
    public const EVENT_OFFER_ACCEPTED = 'offer-accepted';

    protected $table = 'trace_events';

    protected $fillable = [
        'trace_hash',
        'name',
        'context',
    ];

    protected $casts = [
        'context' => 'array',
    ];
}

