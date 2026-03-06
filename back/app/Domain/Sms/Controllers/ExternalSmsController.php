<?php

namespace App\Domain\Sms\Controllers;

use App\Domain\Sms\DTOs\InboundDto;
use App\Domain\Sms\Repositories\SentMessageReceiptRepositoryInterface;
use App\Domain\Sms\Repositories\SmsInboundRepositoryInterface;
use App\Domain\Sms\Repositories\SmsOptinRepositoryInterface;
use App\Domain\Sms\Repositories\SmsOptoutRepositoryInterface;
use App\Domain\Sms\Services\Providers\TwilioProvider;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Throwable;

class ExternalSmsController extends Controller
{
    public function __construct(
        private SentMessageReceiptRepositoryInterface $receiptRepository,
        private SmsInboundRepositoryInterface $inboundRepository,
        private SmsOptinRepositoryInterface $optinRepository,
        private SmsOptoutRepositoryInterface $optoutRepository
    ) {
    }

    public function ingestReceipt(Request $request): Response
    {
        $dto = TwilioProvider::makeReceiptDtoFromRequest($request);

        if (!$dto->getMid() || !$dto->getStatus()) {
            return response()->noContent();
        }

        try {
            $this->receiptRepository->storeDto($dto);
        } catch (Throwable $t) {
            Log::error('failed to store receipt', [
                'signature' => 'Domain.Sms.Controllers.ExternalSmsController',
                'error' => $t->getMessage(),
            ]);
        }

        return response()->noContent();
    }

    public function ingestInbound(Request $request): Response
    {
        $dto = TwilioProvider::makeInboundDtoFromRequest($request);

        if (!$dto->getFrom() || !$dto->getMessage()) {
            return response()->noContent();
        }

        try {
            $this->inboundRepository->storeDto($dto);
        } catch (Throwable $t) {
            Log::error('failed to store inbound', [
                'signature' => 'Domain.Sms.Controllers.ExternalSmsController',
                'error' => $t->getMessage(),
            ]);
            return response()->noContent();
        }

        if ($dto->isOptout()) {
            try {
                $this->optoutRepository->record($dto->getFrom());
            } catch (Throwable $t) {
                Log::error('failed to record optout', [
                    'signature' => 'Domain.Sms.Controllers.ExternalSmsController',
                    'error' => $t->getMessage(),
                ]);
            }
        }

        if ($dto->isOptin()) {
            try {
                $this->optinRepository->record($dto->getFrom(), 'ExternalSmsController@ingestInbound');
            } catch (Throwable $t) {
                Log::error('failed to record optin', [
                    'signature' => 'Domain.Sms.Controllers.ExternalSmsController',
                    'error' => $t->getMessage(),
                ]);
            }
        }

        return response()->noContent();
    }
}

