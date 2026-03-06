<?php

namespace App\Domain\Users\Controllers;

use App\Domain\Users\Actions\UpdateFrontUserProfileAction;
use App\Domain\Users\Requests\UpdateFrontUserProfileRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class FrontUserProfileController extends Controller
{
    public function update(
        UpdateFrontUserProfileRequest $request,
        UpdateFrontUserProfileAction $action,
    ): JsonResponse {
        $profile = $action->execute($request->validated());

        return response()->json($profile);
    }
}



