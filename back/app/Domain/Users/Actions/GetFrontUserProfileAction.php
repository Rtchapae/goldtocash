<?php

namespace App\Domain\Users\Actions;

use App\Domain\Users\Resources\FrontUserResource;
use Illuminate\Support\Facades\Auth;

class GetFrontUserProfileAction
{
    public function execute(): array
    {
        /** @var \App\Domain\Users\Models\User|null $user */
        $user = Auth::guard('front')->user();

        if (! $user) {
            throw new \RuntimeException('User not authenticated');
        }

        return (new FrontUserResource($user))->toArray(request());
    }
}

