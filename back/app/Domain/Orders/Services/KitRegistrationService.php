<?php

namespace App\Domain\Orders\Services;

use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Enums\OrderType;
use App\Domain\Orders\Repositories\OrderRepositoryInterface;
use App\Domain\Users\Repositories\UserRepositoryInterface;
use App\Domain\Sms\Services\Providers\TwilioProvider;
use App\Domain\Sms\Services\SmsUtils;
use App\Domain\Users\Models\VerificationCode;
use App\Domain\Orders\Services\FedexService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Exception;

class KitRegistrationService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly FedexService $fedexService,
    ) {
    }

    public function registerKit(array $validated): array
    {
        $userEmail = $validated['email'] ?? '';
        $user = null;
        $wasAlreadyAuthenticated = false;

        if (Auth::guard('front')->check()) {
            $wasAlreadyAuthenticated = true;
            $user = Auth::guard('front')->user();
        }

        if (!$user) {
            $user = $this->userRepository->findByEmail($userEmail);
        }

        if ($user) {
            try {
                if ($this->hasFedexCredentials()) {
                    if (empty($user->address) || empty($user->city) || empty($user->state) || empty($user->zip)) {
                        throw new Exception('User address is incomplete. Please update your profile.');
                    }

                    $userParams = [
                        'address' => $user->address,
                        'city' => $user->city,
                        'state' => $user->state,
                        'zip' => $user->zip,
                        'phone' => $user->phone,
                        'email' => $user->email,
                    ];

                    if (!$this->fedexService->validateAddress($userParams)) {
                        throw new Exception('Invalid address. Please check your address information.');
                    }

                    $labelData = $this->fedexService->createShippingLabel($user);
                    $track_number = $labelData['track_number'];
                    $label = $labelData['label'];
                    $fedexOrder = $labelData['fedex_order'];

                    $order = $this->orderRepository->create([
                        'user_id' => $user->id,
                        'status' => OrderStatus::KIT_REQUESTED->value,
                        'order_type' => OrderType::ONLINE->value,
                        'welcome' => true,
                        'send_label' => false,
                        'description' => json_encode($fedexOrder),
                    ]);

                    $this->saveShippingLabel($order, $label);

                    $this->generateWelcomeLetterPdf($user, $order, $track_number, $label);
                } else {
                    $order = $this->orderRepository->create([
                        'user_id' => $user->id,
                        'status' => OrderStatus::KIT_REQUESTED->value,
                        'order_type' => OrderType::ONLINE->value,
                        'welcome' => true,
                        'send_label' => false,
                    ]);
                }

                try {
                    $this->orderRepository->sendKitRequestEmail($user, $order);
                } catch (\Exception $e) {
                    Log::error('Failed to send kit request email for existing user', [
                        'user_id' => $user->id,
                        'order_id' => $order->id,
                        'error' => $e->getMessage(),
                    ]);
                }

                return [
                    'status' => true,
                    'message' => $wasAlreadyAuthenticated ? 'Kit created successfully.' : 'Kit created successfully. Confirmation sent to your email.',
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'user' => $user,
                    'order' => $order,
                    'kit_sent' => true,
                ];
            } catch (\Exception $e) {
                Log::error('Failed to create kit order for existing user', [
                    'user_id' => $user->id,
                    'email' => $validated['email'] ?? null,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e;
            }
        }

        $verificationCode = $validated['verification_code'] ?? null;
        $allowUnverified = (bool) ($validated['allow_unverified'] ?? false);
        $phone = $validated['phone'] ?? '';

        $isSmsConfigured = SmsUtils::isSmsConfigured();
        if (!$allowUnverified && !$isSmsConfigured) {
            $allowUnverified = true;
        }

        if (!$verificationCode && !$allowUnverified) {
            try {
                $this->sendVerificationCode($phone);

                return [
                    'status' => true,
                    'requires_verification' => true,
                    'message' => 'Verification code sent to your phone. Please enter the code to continue.',
                ];
            } catch (\Exception $e) {
                Log::error('Failed to send verification code during kit registration', [
                    'phone' => $phone,
                    'error' => $e->getMessage(),
                ]);

                return [
                    'status' => false,
                    'requires_verification' => true,
                    'message' => 'Failed to send verification code. Please check that your phone number is correct and try again.',
                    'error' => $e->getMessage(),
                ];
            }
        }

        if (!$allowUnverified) {
            $isCodeValid = $this->verifyPhoneCode($phone, $verificationCode);

            if (!$isCodeValid) {
                return [
                    'status' => false,
                    'requires_verification' => true,
                    'message' => 'Invalid verification code. Please check that your phone number is correct and try again.',
                ];
            }
        }

        $userPassPlain = Str::random(10);

        $user = $this->userRepository->create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'name' => "{$validated['first_name']} {$validated['last_name']}",
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'address2' => $validated['address2'] ?? null,
            'city' => $validated['city'],
            'state' => $validated['state'],
            'zip' => $validated['zip'],
            'country' => $validated['country'] ?? 'USA',
            'password' => Hash::make($userPassPlain),
        ]);

        if ($this->hasFedexCredentials()) {
            if (empty($user->address) || empty($user->city) || empty($user->state) || empty($user->zip)) {
                throw new Exception('Address information is incomplete.');
            }

            $userParams = [
                'address' => $user->address,
                'city' => $user->city,
                'state' => $user->state,
                'zip' => $user->zip,
                'phone' => $user->phone,
                'email' => $user->email,
            ];

            if (!$this->fedexService->validateAddress($userParams)) {
                throw new Exception('Invalid address. Please check your address information.');
            }

            $labelData = $this->fedexService->createShippingLabel($user);
            $track_number = $labelData['track_number'];
            $label = $labelData['label'];
            $fedexOrder = $labelData['fedex_order'];

            $order = $this->orderRepository->create([
                'user_id' => $user->id,
                'status' => OrderStatus::KIT_REQUESTED->value,
                'order_type' => OrderType::ONLINE->value,
                'welcome' => true,
                'send_label' => false,
                'description' => json_encode($fedexOrder),
            ]);

            try {
                $this->saveShippingLabel($order, $label);
            } catch (\Exception $e) {
                Log::error('saveShippingLabel threw exception', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            $this->generateWelcomeLetterPdf($user, $order, $track_number, $label);
        } else {
            $order = $this->orderRepository->create([
                'user_id' => $user->id,
                'status' => OrderStatus::KIT_REQUESTED->value,
                'order_type' => OrderType::ONLINE->value,
                'welcome' => true,
                'send_label' => false,
            ]);
        }

        /** @var \PHPOpenSourceSaver\JWTAuth\JWTGuard $guard */
        $guard = Auth::guard('front');
        $token = $guard->login($user);

        $this->userRepository->sendPasswordEmail($user, $userPassPlain);

        $this->orderRepository->sendKitRequestEmail($user, $order);

        return [
            'status' => true,
            'message' => 'User registered and logged in successfully',
            'order_id' => $order->id,
            'user_id' => $user->id,
            'access_token' => $token,
            'token_type' => 'bearer',
            'password' => $userPassPlain,
            'user' => $user,
            'order' => $order,
        ];
    }

    private function sendVerificationCode(string $phone): void
    {
        try {
            $code = rand(1111, 9999);

            $normalizedPhone = $this->normalizePhoneNumber($phone);

            $twilioProvider = new TwilioProvider();
            $from = config('services.twilio.from') ?? env('SMS_TWILIO_FROM');

            if (!$from) {
                throw new \Exception('Twilio FROM number not configured');
            }

            $message = "Your verification code is: $code";
            $twilioPhone = preg_replace('/^\+?1?/', '', $normalizedPhone);
            $result = $twilioProvider->send($from, $twilioPhone, $message);

            if ($result->getStatus() === 'failed') {
                $details = $result->getDetails();
                if (str_contains($details, 'At least 1 phone was invalid')) {
                    throw new \Exception('The phone number format is invalid. Please check your phone number and try again.');
                }
                throw new \Exception('Failed to send verification code: ' . $details);
            }

            VerificationCode::create([
                'phone' => $normalizedPhone,
                'code' => (string) $code,
                'active' => 1,
                'attempts' => 0,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send verification code', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Verify phone code
     */
    private function verifyPhoneCode(string $phone, string $code): bool
    {
        $normalizedPhone = $this->normalizePhoneNumber($phone);

        $verificationCode = VerificationCode::where('phone', $normalizedPhone)
            ->where('code', (string) $code)
            ->where('active', 1)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$verificationCode) {
            return false;
        }

        $expiresAt = $verificationCode->created_at->addMinutes(10);
        if (now()->gt($expiresAt)) {
            return false;
        }

        $verificationCode->active = 0;
        $verificationCode->save();

        return true;
    }

    private function hasFedexCredentials(): bool
    {
        $clientId = Config::get('fedex.rest_key');
        $clientSecret = Config::get('fedex.rest_password');

        return !empty($clientId) && !empty($clientSecret);
    }

    private function saveShippingLabel($order, string $label): void
    {
        try {
            $path = storage_path("app/docs/clients/{$order->user_id}/{$order->id}");

            if (!is_dir($path)) {
                $mkdirResult = mkdir($path, 0777, true);
            }

            $imageData = base64_decode($label);
            $filePath = $path . "/shipping-label.png";

            $bytesWritten = file_put_contents($filePath, $imageData);

        } catch (\Exception $e) {
            Log::error('Failed to save shipping label', [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private function generateWelcomeLetterPdf($user, $order, string $track_number, string $label): void
    {
        $letterPath = storage_path("app/docs/clients/{$user->id}/{$order->id}/letter.pdf");

        try {
            $zipForLocations = trim((string) ($user->zip ?? ''));
            if ($zipForLocations === '' && !empty($user->address)) {
                if (preg_match('/\b(\d{5})(?:-\d{4})?\b/', (string) $user->address, $m)) {
                    $zipForLocations = $m[1];
                }
            }
            $locations = $this->fedexService->getLocationsForZip($zipForLocations);

            $logoBase64 = $this->imageToBase64(public_path('images/logo-black-with-gold-c.png'));
            $ratingBase64 = $this->imageToBase64(public_path('images/rating.png'));
            $formBase64 = $this->imageToBase64(public_path('images/form.jpg'));

            $path = dirname($letterPath);
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }

            $pdf = PDF::loadView('emails.welcome-letter', compact('order', 'user', 'track_number', 'locations', 'label', 'logoBase64', 'ratingBase64', 'formBase64'))
                ->setPaper('letter', 'portrait');

            $written = file_put_contents($letterPath, $pdf->output());

            if ($written === false || !file_exists($letterPath) || filesize($letterPath) === 0) {
                throw new \RuntimeException('Failed to save welcome letter PDF.');
            }

        } catch (\Exception $e) {
            Log::error('Failed to generate welcome letter PDF', [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'path' => $letterPath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function regenerateWelcomeLetter($order): bool
    {
        $letterPath = storage_path("app/docs/clients/{$order->user_id}/{$order->id}/letter.pdf");
        if (file_exists($letterPath)) {
            return true;
        }

        $user = $this->userRepository->findById($order->user_id);
        if (!$user) {
            return false;
        }

        $track_number = null;
        if ($order->description) {
            $data = json_decode($order->description);
            if ($data && isset($data->output->transactionShipments[0]->masterTrackingNumber)) {
                $track_number = $data->output->transactionShipments[0]->masterTrackingNumber;
            }
        }
        if (!$track_number) {
            return false;
        }

        $labelPath = storage_path("app/docs/clients/{$order->user_id}/{$order->id}/shipping-label.png");
        if (!file_exists($labelPath)) {
            return false;
        }

        $labelContent = file_get_contents($labelPath);
        if ($labelContent === false) {
            return false;
        }
        $label = base64_encode($labelContent);

        try {
            $this->generateWelcomeLetterPdf($user, $order, $track_number, $label);
            return true;
        } catch (\Exception $e) {
            Log::error('Regenerate letter failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    private function imageToBase64(string $imagePath): ?string
    {
        if (!file_exists($imagePath)) {
            return null;
        }

        try {
            $imageData = file_get_contents($imagePath);
            if ($imageData === false) {
                return null;
            }

            $imageInfo = getimagesize($imagePath);
            if ($imageInfo === false) {
                return null;
            }

            $mimeType = $imageInfo['mime'] ?? 'image/png';
            $base64 = base64_encode($imageData);

            return 'data:' . $mimeType . ';base64,' . $base64;
        } catch (\Exception $e) {
            Log::error('Failed to convert image to base64', [
                'path' => $imagePath,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    private function normalizePhoneNumber(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (strlen($digits) === 11 && str_starts_with($digits, '1')) {
            $digits = substr($digits, 1);
        }

        if (strlen($digits) !== 10) {
            throw new \Exception('Phone number must contain exactly 10 digits after country code removal.');
        }

        return '+1' . $digits;
    }
}

