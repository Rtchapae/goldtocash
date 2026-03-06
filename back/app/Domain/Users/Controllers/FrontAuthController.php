<?php

namespace App\Domain\Users\Controllers;

use App\Domain\Users\Actions\GetFrontUserProfileAction;
use App\Domain\Users\Actions\LoginFrontUserAction;
use App\Domain\Users\Actions\LogoutFrontUserAction;
use App\Domain\Users\Actions\RefreshFrontTokenAction;
use App\Domain\Users\Repositories\UserRepositoryInterface;
use App\Domain\Users\Requests\ForgotPasswordRequest;
use App\Domain\Users\Requests\LoginFrontUserRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FrontAuthController extends Controller
{
    public function login(LoginFrontUserRequest $request, LoginFrontUserAction $action): JsonResponse
    {
        $token = $action->execute($request->credentials());

        return $this->respondWithToken($token, 'front');
    }

    public function forgotPassword(ForgotPasswordRequest $request, UserRepositoryInterface $userRepository): JsonResponse
    {
        $user = $userRepository->findByEmail($request->input('email'));

        if ($user !== null) {
            $plainPassword = Str::password(12, true, true, true, false);
            $userRepository->update($user, ['password' => Hash::make($plainPassword)]);
            $userRepository->sendPasswordEmail($user, $plainPassword);
        }

        return response()->json([
            'message' => 'If an account exists for this email, you will receive a new password shortly.',
        ]);
    }

    public function me(GetFrontUserProfileAction $action): JsonResponse
    {
        return response()->json($action->execute());
    }

    public function logout(LogoutFrontUserAction $action): JsonResponse
    {
        $action->execute();

        return response()->json(['message' => 'Logged out']);
    }

    public function refresh(RefreshFrontTokenAction $action): JsonResponse
    {
        $newToken = $action->execute();

        return $this->respondWithToken($newToken, 'front');
    }

    protected function respondWithToken(string $token, string $guard): JsonResponse
    {
        /** @var \PHPOpenSourceSaver\JWTAuth\JWTGuard $guardInstance */
        $guardInstance = Auth::guard($guard);
        $factory = $guardInstance->factory();

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $factory->getTTL() * 60,
        ]);
    }
}

