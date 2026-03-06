<?php

namespace App\Domain\Users\Controllers;

use App\Domain\Users\Models\User;
use App\Domain\Users\Services\UserNoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserNoteController
{
    public function __construct(
        private readonly UserNoteService $userNoteService
    ) {
    }

    public function query(Request $request): JsonResponse
    {
        $userId = $request->get('userId');

        if (!$userId) {
            return response()->json([
                'error' => 'Invalid user ID.',
            ], 400);
        }

        try {
            $notes = $this->userNoteService->getUserNotes($userId)
                ->map(function ($note) {
                    return [
                        'id' => $note->id,
                        'text' => $note->text,
                        'created_at' => $note->created_at?->format('Y-m-d H:i:s'),
                    ];
                })
                ->toArray();

            $user = User::find($userId);

            return response()->json([
                'data' => [
                    'notes' => $notes,
                    'userName' => $user?->name ?? ($user?->first_name . ' ' . $user?->last_name) ?? 'Unknown',
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to query user notes: ' . $e->getMessage(), [
                'user_id' => $userId,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Could not retrieve notes.',
                'errorDetail' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateOrCreate(Request $request): JsonResponse
    {
        try {
            $userId = $request->get('userId');
            $noteId = $request->get('noteId');
            $text = $request->get('text');

            $this->userNoteService->updateOrCreateNote($userId, $noteId, $text);

            return response()->json([
                'status' => true,
                'message' => 'Note saved successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save note: ' . $e->getMessage(), [
                'user_id' => $request->get('userId'),
                'note_id' => $request->get('noteId'),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => false,
                'error' => 'Could not handle request.',
                'errorDetail' => $e->getMessage(),
            ], 400);
        }
    }

    public function delete(Request $request): JsonResponse
    {
        $noteId = $request->get('id');

        if (!$noteId) {
            return response()->json([
                'status' => false,
                'error' => 'No note ID provided.',
            ], 400);
        }

        try {
            $this->userNoteService->deleteNote($noteId);

            return response()->json([
                'status' => true,
                'message' => 'Note deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete note: ' . $e->getMessage(), [
                'note_id' => $noteId,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => false,
                'error' => 'Could not delete note.',
                'errorDetail' => $e->getMessage(),
            ], 400);
        }
    }
}


