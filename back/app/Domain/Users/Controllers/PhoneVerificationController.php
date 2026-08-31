<?php

namespace App\Domain\Users\Controllers;

use App\Domain\Sms\Services\Providers\TwilioProvider;
use App\Domain\Sms\Services\SmsUtils;
use App\Domain\Users\Models\User;
use App\Domain\Users\Models\VerificationCode;
use App\Domain\Users\Requests\SendVerificationCodeRequest;
use App\Domain\Users\Requests\VerifyCodeRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class PhoneVerificationController extends Controller
{
    public function sendCode(SendVerificationCodeRequest $request): JsonResponse
    {
        try {
            $phone = $request->phone();
            $code = rand(1111, 9999);

            $twilioProvider = new TwilioProvider();
            $from = config('services.twilio.from') ?? env('SMS_TWILIO_FROM');

            if (!$from) {
                throw new \Exception('Twilio FROM number not configured');
            }

            $message = "Your verification code is: $code";
            $result = $twilioProvider->send($from, $phone, $message);

            if ($result->getStatus() === 'failed') {
                throw new \Exception('Failed to send verification code: ' . $result->getDetails());
            }

            VerificationCode::create([
                'phone' => $phone,
                'code' => (string) $code,
                'active' => 1,
                'attempts' => 0,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Verification code sent.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'The verification code could not be sent.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function verifyCode(VerifyCodeRequest $request): JsonResponse
    {
        try {
            $phone = $request->phone();
            $normalizedPhone = preg_replace('/\D/', '', $phone);
            if (strlen($normalizedPhone) === 10) {
                $normalizedPhone = '+1' . $normalizedPhone;
            } elseif (strlen($normalizedPhone) === 11 && str_starts_with($normalizedPhone, '1')) {
                $normalizedPhone = '+' . $normalizedPhone;
            }
            $code = $request->code();

            $verificationCode = VerificationCode::where('phone', $normalizedPhone)
                ->where('code', (string) $code)
                ->where('active', 1)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$verificationCode) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid verification code.',
                ], 400);
            }

            $expiresAt = $verificationCode->created_at->addMinutes(10);
            if (now()->gt($expiresAt)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Verification code has expired.',
                ], 400);
            }

            $verificationCode->active = 0;
            $verificationCode->save();

            $digits10 = SmsUtils::digits10($normalizedPhone);
            if ($digits10 !== '') {
                $candidates = array_values(array_unique(array_filter([
                    $normalizedPhone,
                    $phone,
                    '+1' . $digits10,
                    '1' . $digits10,
                    $digits10,
                ])));
                $user = User::query()->whereIn('phone', $candidates)->first();
                if ($user === null) {
                    $user = User::query()
                        ->whereNotNull('phone')
                        ->where('phone', '!=', '')
                        ->get()
                        ->first(static fn (User $u) => SmsUtils::digits10((string) $u->phone) === $digits10);
                }
                if ($user !== null) {
                    $user->verify = 1;
                    $user->save();
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Phone number verified successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to verify code.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

