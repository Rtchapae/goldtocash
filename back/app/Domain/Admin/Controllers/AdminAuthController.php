<?php

namespace App\Domain\Admin\Controllers;

use App\Domain\Admin\Actions\GetAdminProfileAction;
use App\Domain\Admin\Actions\LoginAdminAction;
use App\Domain\Admin\Actions\LogoutAdminAction;
use App\Domain\Admin\Actions\RefreshAdminTokenAction;
use App\Domain\Admin\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class AdminAuthController extends Controller
{
    public function login(LoginRequest $request, LoginAdminAction $action): JsonResponse
    {
        $token = $action->execute($request->credentials());

        return $this->respondWithToken($token, 'admin');
    }

    public function me(GetAdminProfileAction $action): JsonResponse
    {
        return response()->json($action->execute());
    }

    public function logout(LogoutAdminAction $action): JsonResponse
    {
        $action->execute();

        return response()->json(['message' => 'Logged out']);
    }

    public function refresh(RefreshAdminTokenAction $action): JsonResponse
    {
        $newToken = $action->execute();

        return $this->respondWithToken($newToken, 'admin');
    }

    protected function respondWithToken(string $token, string $guard): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'guard' => $guard,
        ]);
    }
}


