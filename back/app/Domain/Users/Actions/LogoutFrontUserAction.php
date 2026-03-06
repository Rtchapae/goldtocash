<?php

namespace App\Domain\Users\Actions;

use Illuminate\Support\Facades\Auth;

class LogoutFrontUserAction
{
    public function execute(): void
    {
        Auth::guard('front')->logout();
    }
}

