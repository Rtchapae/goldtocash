<?php

namespace App\Domain\Users\Services;

use App\Domain\Users\Models\UserNote;
use Exception;

class UserNoteService
{
    public function updateOrCreateNote(?int $userId, ?int $noteId, string $text): UserNote
    {
        if (!strlen(trim($text))) {
            throw new Exception('Cannot save empty note');
        }

        if ($userId) {
            return $this->createNote($userId, $text);
        } elseif ($noteId) {
            return $this->updateNote($noteId, $text);
        } else {
            throw new Exception('Either userId or noteId must be provided');
        }
    }

    private function createNote(int $userId, string $text): UserNote
    {
        $note = UserNote::create([
            'user_id' => $userId,
            'text' => trim($text),
        ]);

        if (!$note instanceof UserNote) {
            throw new Exception('Could not create note');
        }

        return $note;
    }

    private function updateNote(int $noteId, string $text): UserNote
    {
        $note = UserNote::find($noteId);

        if (!$note) {
            throw new Exception('Note not found');
        }

        $note->update([
            'text' => trim($text),
        ]);

        return $note->fresh();
    }

    public function deleteNote(int $noteId): bool
    {
        $note = UserNote::find($noteId);

        if (!$note) {
            throw new Exception('Note not found');
        }

        return $note->delete();
    }

    public function getUserNotes(int $userId)
    {
        return UserNote::where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->get();
    }
}


