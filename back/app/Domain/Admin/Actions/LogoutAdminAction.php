<?php

namespace App\Domain\Admin\Actions;

use Illuminate\Support\Facades\Auth;

class LogoutAdminAction
{
    public function execute(): void
    {
        Auth::guard('admin')->logout();
    }
}





