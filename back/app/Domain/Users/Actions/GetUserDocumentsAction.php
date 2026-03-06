<?php

namespace App\Domain\Users\Actions;

use App\Domain\Users\Models\User;
use App\Domain\Users\Models\File;
use Illuminate\Support\Facades\Auth;

class GetUserDocumentsAction
{
    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        /** @var User|null $user */
        $user = Auth::guard('front')->user();

        if (! $user) {
            abort(401, 'Unauthorized');
        }

        $files = File::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $baseUrl = config('app.url') . '/api/v1';

        return $files->map(function ($file) use ($baseUrl) {
            return [
                'id' => $file->id,
                'title' => $file->name,
                'type' => $file->description,
                'viewUrl' => "{$baseUrl}/front/user/documents/{$file->id}/view",
                'created_at' => $file->created_at->toISOString(),
            ];
        })->toArray();
    }
}

