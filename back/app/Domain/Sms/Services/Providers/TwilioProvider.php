<?php

namespace App\Domain\Sms\Services\Providers;

use App\Domain\Sms\DTOs\InboundDto;
use App\Domain\Sms\DTOs\SentMessageDto;
use App\Domain\Sms\DTOs\SentMessageReceiptDto;
use App\Domain\Sms\Services\SmsUtils;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Rest\Client;

class TwilioProvider implements ProviderInterface
{
    protected string $name = 'Twilio';
    private Client $sdk;

    /**
     * @throws ConfigurationException
     */
    public function __construct()
    {
        $sid = config('services.twilio.sid') ?? env('SMS_TWILIO_SID');
        $token = config('services.twilio.token') ?? env('SMS_TWILIO_TOKEN');

        if (!$sid || !$token) {
            throw new ConfigurationException('Twilio credentials not configured');
        }

        $this->sdk = new Client($sid, $token);
    }

    /**
     * @throws Exception
     */
    public function send(string $from, string $to, string $message): SentMessageDto
    {
        \Illuminate\Support\Facades\Log::info('Twilio send attempt', [
            'from' => $from,
            'to' => $to,
            'message_length' => strlen($message),
            'message_preview' => substr($message, 0, 50)
        ]);

        $sm = (new SentMessageDto)
            ->setProviderName($this->name)
            ->setFrom($from)
            ->setTo($to)
            ->setMessage($message);

        try {
            $this->throwIfInvalidPhoneInArray([$from, $to]);
        } catch (\Throwable $t) {
            \Illuminate\Support\Facades\Log::error('Twilio phone validation failed', [
                'from' => $from,
                'to' => $to,
                'validation_error' => $t->getMessage()
            ]);
            $sm->setStatus('failed')
                ->setDetails('At least 1 phone was invalid');
            return $sm;
        }

        $originalFrom = $from;
        $originalTo = $to;
        $from = $this->ensureCountryCode($from);
        $to = $this->ensureCountryCode($to);

        \Illuminate\Support\Facades\Log::info('Twilio phone numbers after country code ensure', [
            'original_from' => $originalFrom,
            'original_to' => $originalTo,
            'ensured_from' => $from,
            'ensured_to' => $to
        ]);

        try {
            $this->hitApi($sm, $from, $to);
        } catch (\Throwable $t) {
            $sm->setStatus('failed');
            $sm->setDetails($t->getMessage());
        }

        return $sm;
    }

    /**
     * @throws Exception
     */
    protected function hitApi(SentMessageDto &$sm, string $from, string $to): void
    {
        // Use frontend URL for status callback (Twilio needs a public URL)
        $frontendUrl = config('app.frontend_url', config('app.url'));
        $statusCallback = $frontendUrl . '/api/sms/ingest-receipt';

        try {
            $messageInstance = $this->sdk->messages->create($to, [
                'from' => $from,
                'body' => $sm->getMessage(),
                'statusCallback' => $statusCallback,
            ]);

            $sm->setStatus($messageInstance->status);
            $sm->setMid($messageInstance->sid);
            $sm->setSubmittedAt(Carbon::parse($messageInstance->dateCreated)->setTimezone(config('app.timezone')));
        } catch (\Throwable $t) {
            $errorDetails = $t->getMessage();
            $sm->setDetails($errorDetails);

            // Log full Twilio error details
            \Illuminate\Support\Facades\Log::error('Twilio API error', [
                'error_message' => $errorDetails,
                'error_code' => $t->getCode(),
                'from' => $from,
                'to' => $to,
                'message_length' => strlen($sm->getMessage()),
                'exception_class' => get_class($t),
                'trace' => $t->getTraceAsString()
            ]);

            throw new Exception('failed to hit api: ' . $errorDetails);
        }
    }

    public static function makeReceiptDtoFromRequest(Request $request): SentMessageReceiptDto
    {
        return (new SentMessageReceiptDto)
            ->setMid($request->get('MessageSid') ?: '')
            ->setStatus($request->get('MessageStatus') ?: '');
    }

    public static function makeInboundDtoFromRequest(Request $request): InboundDto
    {
        return (new InboundDto)
            ->setFrom($request->get('From') ?: '')
            ->setTo($request->get('To') ?: '')
            ->setMessage($request->get('Body') ?: '');
    }

    protected function ensureCountryCode(string $phone): string
    {
        return '+1' . preg_replace('/^\+?1?/', '', $phone);
    }

    /**
     * @param array<string> $phones
     *
     * @throws Exception
     */
    private function throwIfInvalidPhoneInArray(array $phones): void
    {
        foreach ($phones as $phone) {
            if (!SmsUtils::isValidPhone($phone)) {
                throw new Exception('phone is not valid: ' . $phone);
            }
        }
    }
}

