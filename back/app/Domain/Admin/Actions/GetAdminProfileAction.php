<?php

namespace App\Domain\Admin\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Authenticatable;

class GetAdminProfileAction
{
    public function execute(): ?Authenticatable
    {
        return Auth::guard('admin')->user();
    }
}





