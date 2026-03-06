<?php

namespace App\Domain\Admin\Actions;

use Illuminate\Support\Facades\Auth;

class RefreshAdminTokenAction
{
    public function execute(): string
    {
        /** @var \PHPOpenSourceSaver\JWTAuth\JWTGuard $guard */
        $guard = Auth::guard('admin');

        return $guard->refresh();
    }
}





