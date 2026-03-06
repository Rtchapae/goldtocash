<?php

namespace App\Domain\Users\Actions;

use App\Domain\Users\Models\File;
use App\Domain\Users\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class UploadDocumentAction
{
    public function execute(array $data): array
    {
        /** @var User|null $user */
        $user = Auth::guard('front')->user();

        if (! $user) {
            abort(401, 'Unauthorized');
        }

        /** @var UploadedFile $file */
        $file = $data['file'];
        $type = $data['type'];

        $filename = $type . '.' . $file->getClientOriginalExtension();

        $path = storage_path("app/private/docs/clients/{$user->id}/shared/");
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $file->move($path, $filename);

        $fileRecord = File::create([
            'name' => $filename,
            'user_id' => $user->id,
            'description' => $type,
            'path' => '',
        ]);

        $baseUrl = config('app.url') . '/api/v1';

        return [
            'id' => $fileRecord->id,
            'title' => $filename,
            'type' => $type,
            'viewUrl' => "{$baseUrl}/front/user/documents/{$fileRecord->id}/view",
            'created_at' => $fileRecord->created_at->toISOString(),
        ];
    }
}

