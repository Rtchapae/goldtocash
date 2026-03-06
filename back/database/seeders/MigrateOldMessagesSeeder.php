<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MigrateOldMessagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Migrates old messages data to new structure:
     * - Ensures all threads exist in new threads table
     * - Migrates messages to new messages table
     * - Creates participants for all threads
     */
    public function run(): void
    {
        $this->command->info('Starting migration of old messages data...');

        try {
            // Step 1: Migrate threads
            $this->migrateThreads();

            // Step 2: Migrate messages
            $this->migrateMessages();

            // Step 3: Migrate participants
            $this->migrateParticipants();

            $this->command->info('Migration completed successfully!');
        } catch (\Exception $e) {
            $this->command->error('Migration failed: ' . $e->getMessage());
            Log::error('Messages migration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Migrate threads from old structure to new
     */
    private function migrateThreads(): void
    {
        $this->command->info('Migrating threads...');

        // Check if old threads table exists
        if (!DB::getSchemaBuilder()->hasTable('threads')) {
            $this->command->warn('Threads table does not exist. Skipping threads migration.');
            return;
        }

        // Get all threads from old table that don't exist in new table
        $oldThreads = DB::table('threads')
            ->whereNull('deleted_at')
            ->get();

        $migrated = 0;
        $skipped = 0;
        $updated = 0;

        foreach ($oldThreads as $oldThread) {
            // Check if thread already exists in new structure
            $exists = DB::table('threads')
                ->where('id', $oldThread->id)
                ->exists();

            if ($exists) {
                // Update existing thread to ensure subject is 'Technical Support'
                $currentThread = DB::table('threads')->where('id', $oldThread->id)->first();
                if (!$currentThread->subject || $currentThread->subject !== 'Technical Support') {
                    DB::table('threads')
                        ->where('id', $oldThread->id)
                        ->update([
                            'subject' => 'Technical Support',
                            'updated_at' => now(),
                        ]);
                    $updated++;
                }
                $skipped++;
                continue;
            }

            // Insert thread into new structure
            DB::table('threads')->insert([
                'id' => $oldThread->id,
                'subject' => 'Technical Support', // Always set to Technical Support for support chats
                'slug' => $oldThread->slug ?? md5('thread_' . $oldThread->id . '_' . time()),
                'max_participants' => $oldThread->max_participants ?? null,
                'start_date' => $oldThread->start_date ?? null,
                'end_date' => $oldThread->end_date ?? null,
                'avatar' => $oldThread->avatar ?? null,
                'created_at' => $oldThread->created_at ?? now(),
                'updated_at' => $oldThread->updated_at ?? now(),
                'deleted_at' => $oldThread->deleted_at ?? null,
            ]);

            $migrated++;
        }

        $this->command->info("Threads: {$migrated} migrated, {$skipped} skipped, {$updated} updated");

        // Also update all existing threads to have 'Technical Support' subject if they don't have it
        $this->updateAllThreadsSubject();
    }

    /**
     * Update all threads to have 'Technical Support' subject
     */
    private function updateAllThreadsSubject(): void
    {
        $this->command->info('Updating all threads subject to "Technical Support"...');

        $updated = DB::table('threads')
            ->whereNull('deleted_at')
            ->where(function ($query) {
                $query->whereNull('subject')
                    ->orWhere('subject', '!=', 'Technical Support');
            })
            ->update([
                'subject' => 'Technical Support',
                'updated_at' => now(),
            ]);

        $this->command->info("Updated {$updated} threads with 'Technical Support' subject");
    }

    /**
     * Migrate messages from old structure to new
     */
    private function migrateMessages(): void
    {
        $this->command->info('Migrating messages...');

        // Check if old messages table exists
        if (!DB::getSchemaBuilder()->hasTable('messages')) {
            $this->command->warn('Messages table does not exist. Skipping messages migration.');
            return;
        }

        // Get all messages from old table that don't exist in new table
        $oldMessages = DB::table('messages')
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->get();

        $migrated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($oldMessages as $oldMessage) {
            try {
                // Check if message already exists in new structure
                $exists = DB::table('messages')
                    ->where('id', $oldMessage->id)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                // Verify thread exists
                $threadExists = DB::table('threads')
                    ->where('id', $oldMessage->thread_id)
                    ->exists();

                if (!$threadExists) {
                    $this->command->warn("Thread {$oldMessage->thread_id} does not exist for message {$oldMessage->id}. Skipping.");
                    $errors++;
                    continue;
                }

                // Verify user exists
                $userExists = DB::table('users')
                    ->where('id', $oldMessage->user_id)
                    ->exists();

                if (!$userExists) {
                    $this->command->warn("User {$oldMessage->user_id} does not exist for message {$oldMessage->id}. Skipping.");
                    $errors++;
                    continue;
                }

                // Insert message into new structure
                DB::table('messages')->insert([
                    'id' => $oldMessage->id,
                    'thread_id' => $oldMessage->thread_id,
                    'user_id' => $oldMessage->user_id,
                    'body' => $oldMessage->body ?? '',
                    'read' => isset($oldMessage->read) ? (int)$oldMessage->read : null,
                    'created_at' => $oldMessage->created_at ?? now(),
                    'updated_at' => $oldMessage->updated_at ?? now(),
                    'deleted_at' => $oldMessage->deleted_at ?? null,
                ]);

                $migrated++;
            } catch (\Exception $e) {
                $this->command->error("Error migrating message {$oldMessage->id}: " . $e->getMessage());
                $errors++;
                Log::error("Failed to migrate message", [
                    'message_id' => $oldMessage->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->command->info("Messages: {$migrated} migrated, {$skipped} skipped, {$errors} errors");
    }

    /**
     * Migrate participants from old structure to new
     */
    private function migrateParticipants(): void
    {
        $this->command->info('Migrating participants...');

        // Check if old participants table exists
        if (!DB::getSchemaBuilder()->hasTable('participants')) {
            $this->command->warn('Participants table does not exist. Creating participants from messages...');
            $this->createParticipantsFromMessages();
            return;
        }

        // Get all participants from old table
        $oldParticipants = DB::table('participants')
            ->whereNull('deleted_at')
            ->get();

        $migrated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($oldParticipants as $oldParticipant) {
            try {
                // Check if participant already exists
                $exists = DB::table('participants')
                    ->where('thread_id', $oldParticipant->thread_id)
                    ->where('user_id', $oldParticipant->user_id)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                // Verify thread exists
                $threadExists = DB::table('threads')
                    ->where('id', $oldParticipant->thread_id)
                    ->exists();

                if (!$threadExists) {
                    $this->command->warn("Thread {$oldParticipant->thread_id} does not exist for participant. Skipping.");
                    $errors++;
                    continue;
                }

                // Verify user exists
                $userExists = DB::table('users')
                    ->where('id', $oldParticipant->user_id)
                    ->exists();

                if (!$userExists) {
                    $this->command->warn("User {$oldParticipant->user_id} does not exist for participant. Skipping.");
                    $errors++;
                    continue;
                }

                // Insert participant into new structure
                DB::table('participants')->insert([
                    'thread_id' => $oldParticipant->thread_id,
                    'user_id' => $oldParticipant->user_id,
                    'last_read' => $oldParticipant->last_read ?? null,
                    'starred' => isset($oldParticipant->starred) ? (bool)$oldParticipant->starred : false,
                    'created_at' => $oldParticipant->created_at ?? now(),
                    'updated_at' => $oldParticipant->updated_at ?? now(),
                    'deleted_at' => $oldParticipant->deleted_at ?? null,
                ]);

                $migrated++;
            } catch (\Exception $e) {
                $this->command->error("Error migrating participant: " . $e->getMessage());
                $errors++;
                Log::error("Failed to migrate participant", [
                    'thread_id' => $oldParticipant->thread_id ?? null,
                    'user_id' => $oldParticipant->user_id ?? null,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->command->info("Participants: {$migrated} migrated, {$skipped} skipped, {$errors} errors");

        // Also create participants from messages if they don't exist
        $this->createParticipantsFromMessages();
    }

    /**
     * Create participants from messages (for threads that don't have participants)
     */
    private function createParticipantsFromMessages(): void
    {
        $this->command->info('Creating missing participants from messages...');

        // Get unique thread_id + user_id combinations from messages
        $messageParticipants = DB::table('messages')
            ->whereNull('deleted_at')
            ->select('thread_id', 'user_id')
            ->distinct()
            ->get();

        $created = 0;
        $skipped = 0;

        foreach ($messageParticipants as $mp) {
            // Check if participant already exists
            $exists = DB::table('participants')
                ->where('thread_id', $mp->thread_id)
                ->where('user_id', $mp->user_id)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            // Verify thread exists
            $threadExists = DB::table('threads')
                ->where('id', $mp->thread_id)
                ->exists();

            if (!$threadExists) {
                continue;
            }

            // Verify user exists
            $userExists = DB::table('users')
                ->where('id', $mp->user_id)
                ->exists();

            if (!$userExists) {
                continue;
            }

            // Create participant
            DB::table('participants')->insert([
                'thread_id' => $mp->thread_id,
                'user_id' => $mp->user_id,
                'last_read' => null,
                'starred' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $created++;
        }

        $this->command->info("Created {$created} participants from messages, {$skipped} already existed");

        // Also ensure all threads have at least one participant (user)
        $this->ensureAllThreadsHaveParticipants();
    }

    /**
     * Ensure all threads have at least one participant (user)
     */
    private function ensureAllThreadsHaveParticipants(): void
    {
        $this->command->info('Ensuring all threads have participants...');

        // Get all threads that don't have participants
        $threadsWithoutParticipants = DB::table('threads')
            ->whereNull('deleted_at')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('participants')
                    ->whereColumn('participants.thread_id', 'threads.id')
                    ->whereNull('participants.deleted_at');
            })
            ->get();

        $created = 0;

        foreach ($threadsWithoutParticipants as $thread) {
            // Try to find a user from messages in this thread
            $messageUser = DB::table('messages')
                ->where('thread_id', $thread->id)
                ->whereNull('deleted_at')
                ->whereNotIn('user_id', function ($query) {
                    // Exclude admin users (users with role_id pointing to admin role)
                    $query->select('users.id')
                        ->from('users')
                        ->join('roles', 'users.role_id', '=', 'roles.id')
                        ->whereIn('roles.name', ['admin', 'manager']);
                })
                ->select('user_id')
                ->first();

            if ($messageUser) {
                // Create participant from message user
                DB::table('participants')->insert([
                    'thread_id' => $thread->id,
                    'user_id' => $messageUser->user_id,
                    'last_read' => null,
                    'starred' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $created++;
            } else {
                $this->command->warn("Thread {$thread->id} has no messages with regular users. Cannot create participant.");
            }
        }

        $this->command->info("Created {$created} participants for threads that were missing them");
    }
}

