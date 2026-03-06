<?php

namespace App\Domain\Users\Actions;

use App\Domain\Users\Models\User;
use App\Domain\Users\Repositories\UserRepositoryInterface;
use App\Domain\Users\Resources\FrontUserResource;
use Illuminate\Support\Facades\Auth;

class UpdateFrontUserProfileAction
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
    ) {
    }

    public function execute(array $data): array
    {
        /** @var User|null $user */
        $user = Auth::guard('front')->user();

        if (! $user) {
            abort(401, 'Unauthorized');
        }

        $allowedKeys = [
            'first_name',
            'last_name',
            'email',
            'phone',
            'address',
            'city',
            'state',
            'zip',
            'payment_method',
            'payment_method_params',
            'government_id',
            'government_id_params',
            'date_of_birth',
        ];

        /** @var array<string, mixed> $update */
        $update = array_intersect_key($data, array_flip($allowedKeys));

        if (array_key_exists('first_name', $update) || array_key_exists('last_name', $update)) {
            $first = (string) ($update['first_name'] ?? $user->first_name ?? '');
            $last = (string) ($update['last_name'] ?? $user->last_name ?? '');
            $update['name'] = trim(trim($first) . ' ' . trim($last));
        }

        if (isset($update['payment_method_params']) && is_array($update['payment_method_params'])) {
            $update['payment_method_params'] = json_encode($update['payment_method_params']);
        }

        if (isset($update['government_id_params']) && is_array($update['government_id_params'])) {
            $update['government_id_params'] = json_encode($update['government_id_params']);
        }

        $updated = $this->users->update($user, $update);

        return (new FrontUserResource($updated))->toArray(request());
    }
}


