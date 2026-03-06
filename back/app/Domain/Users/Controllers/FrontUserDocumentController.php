<?php

namespace App\Domain\Users\Controllers;

use App\Domain\Users\Actions\UploadDocumentAction;
use App\Domain\Users\Actions\GetUserDocumentsAction;
use App\Domain\Users\Requests\UploadDocumentRequest;
use App\Domain\Users\Models\File;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FrontUserDocumentController extends Controller
{
    public function index(GetUserDocumentsAction $action): JsonResponse
    {
        $documents = $action->execute();

        return response()->json([
            'status' => true,
            'documents' => $documents,
        ]);
    }

    public function upload(
        UploadDocumentRequest $request,
        UploadDocumentAction $action,
    ): JsonResponse {
        $data = [
            'type' => $request->input('type'),
            'file' => $request->file('file'),
        ];

        $result = $action->execute($data);

        return response()->json([
            'status' => true,
            'document' => $result,
        ]);
    }

    public function view(int $id): Response
    {
        /** @var \App\Domain\Users\Models\User|null $user */
        $user = Auth::guard('front')->user();

        if (! $user) {
            abort(401, 'Unauthorized');
        }

        $file = File::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $filepath = storage_path("app/private/docs/clients/{$user->id}/shared/{$file->name}");

        if (!file_exists($filepath)) {
            abort(404, 'File not found');
        }

        $fileBody = file_get_contents($filepath);
        $mimeType = mime_content_type($filepath);

        return response($fileBody, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="docs-' . $file->name . '"',
        ]);
    }
}

