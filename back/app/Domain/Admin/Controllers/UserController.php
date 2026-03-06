<?php

namespace App\Domain\Admin\Controllers;

use App\Domain\Users\Actions\ListUsersAction;
use App\Http\Controllers\Controller;
use App\Domain\Admin\Requests\IndexRequest;
use App\Domain\Admin\Requests\CreateUserRequest;
use App\Domain\Admin\Resources\UserResource;
use App\Domain\Admin\Resources\UserResourceCollection;
use App\Domain\Users\Repositories\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function index(IndexRequest $request, ListUsersAction $action): JsonResponse
    {
        $admin = $request->user('admin');

        $paginator = $action->execute(
            $request->perPage(),
            $request->search(),
            $admin?->id
        );

        return response()->json(new UserResourceCollection(
            UserResource::collection($paginator->items()),
            $paginator
        ));
    }

    public function show(int $id): JsonResponse
    {
        $user = $this->userRepository->findByIdWith($id, ['orders']);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json(new UserResource($user));
    }

    public function store(CreateUserRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $isOfflineOrder = $request->input('is_offline_order', false);

            $userPassPlain = Str::random(10);

            $email = $isOfflineOrder
                ? ($validated['email'] ?? 'ikostyan88@gmail.com')
                : $validated['email'];

            $user = $this->userRepository->create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'name' => $validated['name'],
                'email' => $email,
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'address2' => $validated['address2'] ?? null,
                'city' => $validated['city'],
                'state' => $validated['state'],
                'zip' => $validated['zip'],
                'country' => $validated['country'] ?? 'USA',
                'password' => Hash::make($userPassPlain),
            ]);

            if (!$isOfflineOrder) {
                $this->userRepository->sendPasswordEmail($user, $userPassPlain);
            }

            return response()->json([
                'status' => true,
                'message' => 'User created successfully',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create user in admin', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to create user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}


