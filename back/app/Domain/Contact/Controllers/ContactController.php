<?php

namespace App\Domain\Contact\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    private const CAPTCHA_TTL_SECONDS = 600;

    public function captcha(): JsonResponse
    {
        $a = random_int(1, 9);
        $b = random_int(1, 9);
        $expires = time() + self::CAPTCHA_TTL_SECONDS;
        $token = $this->signCaptcha($a, $b, $expires);

        return response()->json([
            'data' => [
                'a' => $a,
                'b' => $b,
                'expires' => $expires,
                'token' => $token,
                'question' => "{$a} + {$b}",
            ],
        ]);
    }

    public function send(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'message' => ['required', 'string', 'max:999'],
            'captcha_a' => ['required', 'integer', 'min:1', 'max:9'],
            'captcha_b' => ['required', 'integer', 'min:1', 'max:9'],
            'captcha_expires' => ['required', 'integer'],
            'captcha_token' => ['required', 'string'],
            'captcha_answer' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        if (! $this->verifyCaptcha(
            (int) $data['captcha_a'],
            (int) $data['captcha_b'],
            (int) $data['captcha_expires'],
            (string) $data['captcha_token'],
            (int) $data['captcha_answer'],
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect captcha answer. Please try again.',
            ], 422);
        }

        $to = config('fedex.parcel_options.recipient_email', 'hello@goldtocash.us')
            ?: 'hello@goldtocash.us';

        $viewData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'body' => $data['message'],
        ];

        try {
            Mail::send('emails.contact', $viewData, function ($mailMessage) use ($data, $to) {
                $mailMessage->to($to)
                    ->replyTo($data['email'], $data['name'])
                    ->subject('Contact form: ' . $data['name']);
            });
        } catch (\Throwable $e) {
            Log::error('Contact form mail failed', [
                'error' => $e->getMessage(),
                'email' => $data['email'],
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Sorry, something went wrong. Please try again later or email hello@goldtocash.us.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Your message has been sent. We will get back to you soon!',
        ]);
    }

    private function signCaptcha(int $a, int $b, int $expires): string
    {
        return hash_hmac('sha256', "{$a}:{$b}:{$expires}", (string) config('app.key'));
    }

    private function verifyCaptcha(int $a, int $b, int $expires, string $token, int $answer): bool
    {
        if ($expires < time()) {
            return false;
        }

        $expected = $this->signCaptcha($a, $b, $expires);
        if (! hash_equals($expected, $token)) {
            return false;
        }

        return $answer === ($a + $b);
    }
}
