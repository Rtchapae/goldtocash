<?php

namespace App\Domain\Sms\Services\Providers;

use App\Domain\Sms\DTOs\InboundDto;
use App\Domain\Sms\DTOs\SentMessageDto;
use App\Domain\Sms\DTOs\SentMessageReceiptDto;
use Illuminate\Http\Request;

interface ProviderInterface
{
    public function send(string $from, string $to, string $message): SentMessageDto;

    public static function makeReceiptDtoFromRequest(Request $request): SentMessageReceiptDto;

    public static function makeInboundDtoFromRequest(Request $request): InboundDto;
}

