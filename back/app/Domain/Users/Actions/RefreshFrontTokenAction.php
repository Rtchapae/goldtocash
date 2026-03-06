<?php

namespace App\Domain\Users\Actions;

use Illuminate\Support\Facades\Auth;

class RefreshFrontTokenAction
{
    public function execute(): string
    {
        /** @var \PHPOpenSourceSaver\JWTAuth\JWTGuard $guard */
        $guard = Auth::guard('front');
        
        return $guard->refresh();
    }
}

