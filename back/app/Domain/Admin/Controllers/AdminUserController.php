<?php

namespace App\Domain\Admin\Controllers;

use App\Domain\Admin\Actions\ListAdminUsersAction;
use App\Domain\Admin\Actions\CreateAdminUserAction;
use App\Domain\Admin\Actions\UpdateAdminUserAction;
use App\Domain\Admin\Actions\DeleteAdminUserAction;
use App\Domain\Admin\Resources\AdminUserResource;
use App\Domain\Admin\Resources\AdminUserResourceCollection;
use App\Domain\Admin\Requests\ListAdminUsersRequest;
use App\Domain\Admin\Requests\CreateAdminUserRequest;
use App\Domain\Admin\Requests\UpdateAdminUserRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class AdminUserController extends Controller
{
    public function __construct(
        private readonly ListAdminUsersAction $listAction,
        private readonly CreateAdminUserAction $createAction,
        private readonly UpdateAdminUserAction $updateAction,
        private readonly DeleteAdminUserAction $deleteAction,
    ) {
    }

    public function index(ListAdminUsersRequest $request): JsonResponse
    {
        $paginator = $this->listAction->execute(
            perPage: $request->perPage(),
            page: $request->page(),
            search: $request->search(),
            role: $request->role(),
        );

        return response()->json(new AdminUserResourceCollection(
            AdminUserResource::collection($paginator->items()),
            $paginator
        ));
    }

    public function store(CreateAdminUserRequest $request): JsonResponse
    {
        try {
            $user = $this->createAction->execute($request->validated());
            return response()->json(new AdminUserResource($user), 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create admin user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateAdminUserRequest $request, int $id): JsonResponse
    {
        try {
            $user = $this->updateAction->execute($id, $request->validated());
            return response()->json(new AdminUserResource($user));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update admin user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->deleteAction->execute($id);
            return response()->json(['message' => 'Admin user deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete admin user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

