<?php

namespace App\Domain\Admin\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginAdminAction
{
    public function execute(array $credentials): string
    {
        /** @var \PHPOpenSourceSaver\JWTAuth\JWTGuard $guard */
        $guard = Auth::guard('admin');

        if (! $token = $guard->attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        $user = $guard->user();

        if (! $user || (! $user->isAdmin() && ! $user->isManager())) {
            $guard->logout();

            throw ValidationException::withMessages([
                'email' => ['Forbidden.'],
            ]);
        }

        return $token;
    }
}





