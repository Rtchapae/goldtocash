<?php

namespace App\Domain\Admin\Actions;

use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Enums\OrderType;
use App\Domain\Orders\Repositories\OrderRepositoryInterface;
use App\Domain\Orders\Support\OfflineOrderPdfData;
use App\Domain\Users\Enums\UserRole;
use App\Domain\Users\Models\User;
use App\Domain\Users\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Exception;

class CreateOrderAction
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function execute(array $data): array
    {
        /** @var User|null $adminUser */
        $adminUser = Auth::guard('admin')->user();

        if (!$adminUser) {
            throw ValidationException::withMessages([
                'message' => ['Unauthorized'],
            ]);
        }

        $orderType = $data['order_type'] ?? $this->determineOrderType($adminUser, $data);
        $branchId = $this->determineBranchId($adminUser, $data, $orderType);

        if ($orderType === OrderType::OFFLINE->value && !$branchId) {
            throw ValidationException::withMessages([
                'branch_id' => ['Branch is required for offline orders'],
            ]);
        }

        try {
            $userId = $data['user_id'] ?? null;

            if (!$userId) {
                if ($orderType === OrderType::OFFLINE->value && isset($data['user_data'])) {
                    $userId = $this->createUserForOfflineOrder($data['user_data']);
                } else {
                    throw ValidationException::withMessages([
                        'user_id' => ['User ID required or user_data for offline orders'],
                    ]);
                }
            }

            $user = $this->userRepository->findById($userId);
            if (!$user) {
                throw ValidationException::withMessages([
                    'user_id' => ['User not found'],
                ]);
            }

            if ($orderType === OrderType::OFFLINE->value) {
                $this->syncOfflineUserProfile($user, $data);
            }

            $hasExistingOrders = $this->orderRepository->userHasOtherOrders($userId, 0);

            $status = ($orderType === OrderType::OFFLINE->value)
                ? OrderStatus::PAID->value
                : OrderStatus::KIT_REQUESTED->value;

            $notes = $data['notes'] ?? null;
            if (! empty($data['items_description']) && is_array($data['items_description'])) {
                $notes = OfflineOrderPdfData::encodeItems($data['items_description']);
            }

            $orderData = [
                'user_id' => $userId,
                'status' => $status,
                'order_type' => $orderType,
                'branch_id' => $branchId,
                'notes' => $notes,
                'welcome' => true,
                'send_label' => false,
            ];

            if ($orderType === OrderType::OFFLINE->value && isset($data['amount'])) {
                $orderData['amount'] = $data['amount'];
            }

            if ($orderType === OrderType::OFFLINE->value && isset($data['payment_method'])) {
                $orderData['payment_method'] = $data['payment_method'];
            }

            $order = $this->orderRepository->create($orderData);

            if ($orderType === OrderType::ONLINE->value) {
                $this->orderRepository->sendKitRequestEmail($user, $order);
            }

            return [
                'status' => true,
                'message' => 'Order created successfully',
                'order_id' => $order->id,
                'order' => [
                    'id' => $order->id,
                    'status' => $order->status,
                    'order_type' => $order->order_type,
                    'branch_id' => $order->branch_id,
                    'created_at' => $order->created_at->toISOString(),
                ],
            ];
        } catch (Exception $e) {
            Log::warning("Failed to create order in admin", [
                'message' => $e->getMessage(),
                'adminUserId' => $adminUser->id,
                'data' => $data,
            ]);

            throw $e;
        }
    }

    private function createUserForOfflineOrder(array $userData): int
    {
        $userPassPlain = Str::random(10);

        $email = $userData['email'] ?? $this->generateOfflineEmail();

        $existingUser = $this->userRepository->findByEmail($email);
        if ($existingUser) {
            return $existingUser->id;
        }

        $user = $this->userRepository->create([
            'first_name' => $userData['first_name'] ?? '',
            'last_name' => $userData['last_name'] ?? '',
            'name' => $userData['name'] ?? '',
            'email' => $email,
            'phone' => $userData['phone'] ?? '',
            'address' => $userData['address'] ?? '',
            'address2' => $userData['address2'] ?? null,
            'city' => $userData['city'] ?? '',
            'state' => $userData['state'] ?? '',
            'zip' => $userData['zip'] ?? '',
            'country' => $userData['country'] ?? 'USA',
            'date_of_birth' => $userData['date_of_birth'] ?? null,
            'government_id' => $userData['government_id'] ?? 'state',
            'government_id_params' => $this->encodeGovernmentIdParams($userData),
            'password' => Hash::make($userPassPlain),
        ]);

        return $user->id;
    }

    private function generateOfflineEmail(): string
    {
        do {
            $randomString = Str::random(12);
            $email = $randomString . '@goldtocash.us';
        } while ($this->userRepository->findByEmail($email));

        return $email;
    }

    private function determineOrderType(User $adminUser, array $data): string
    {
        if ($adminUser->isManager()) {
            return OrderType::OFFLINE->value;
        }

        if ($adminUser->isAdmin()) {
            return $data['order_type'] ?? OrderType::ONLINE->value;
        }

        return OrderType::ONLINE->value;
    }

    private function determineBranchId(User $adminUser, array $data, string $orderType): ?int
    {
        if ($adminUser->isManager() && $orderType === OrderType::OFFLINE->value) {
            return $adminUser->branch_id;
        }

        if ($adminUser->isAdmin() && $orderType === OrderType::OFFLINE->value) {
            return $data['branch_id'] ?? null;
        }

        return null;
    }

    private function syncOfflineUserProfile(User $user, array $data): void
    {
        $updates = [];

        $userData = $data['user_data'] ?? [];
        $fieldMap = [
            'name' => 'name',
            'email' => 'email',
            'phone' => 'phone',
            'address' => 'address',
            'address2' => 'address2',
            'city' => 'city',
            'state' => 'state',
            'zip' => 'zip',
            'country' => 'country',
        ];

        foreach ($fieldMap as $inputKey => $column) {
            $value = $userData[$inputKey] ?? $data[$inputKey] ?? null;
            if ($value !== null && $value !== '') {
                $updates[$column] = $value;
            }
        }

        $dob = $data['date_of_birth'] ?? $userData['date_of_birth'] ?? null;
        if ($dob) {
            $updates['date_of_birth'] = $dob;
        }

        $govNumber = $data['government_id_number'] ?? $userData['government_id_number'] ?? null;
        $stateIssued = $data['state_issued'] ?? $userData['state_issued'] ?? null;
        if ($govNumber || $stateIssued) {
            $updates['government_id'] = $user->government_id ?: 'state';
            $updates['government_id_params'] = json_encode([
                'idNumber' => $govNumber ?: (json_decode((string) $user->government_id_params, true)['idNumber'] ?? ''),
                'issuer' => $stateIssued ?: (json_decode((string) $user->government_id_params, true)['issuer'] ?? ''),
            ]);
        }

        if ($updates !== []) {
            $this->userRepository->update($user, $updates);
        }
    }

    /**
     * @param  array<string, mixed>  $userData
     */
    private function encodeGovernmentIdParams(array $userData): ?string
    {
        $idNumber = $userData['government_id_number'] ?? null;
        $issuer = $userData['state_issued'] ?? null;

        if (! $idNumber && ! $issuer) {
            return null;
        }

        return json_encode([
            'idNumber' => $idNumber ?? '',
            'issuer' => $issuer ?? '',
        ]);
    }

}

