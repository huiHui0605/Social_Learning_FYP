<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Message;
use App\Models\User;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users for testing
        $lecturers = User::where('role', 'lecturer')->take(2)->get();
        $students = User::where('role', 'student')->take(3)->get();

        if ($lecturers->isEmpty() || $students->isEmpty()) {
            $this->command->info('No lecturers or students found. Skipping message seeding.');
            return;
        }

        $sampleMessages = [
            [
                'content' => 'Hello! I have a question about the assignment.',
                'message_type' => 'text',
            ],
            [
                'content' => 'Sure, what would you like to know?',
                'message_type' => 'text',
            ],
            [
                'content' => 'I\'m confused about the requirements for question 3.',
                'message_type' => 'text',
            ],
            [
                'content' => 'Let me clarify that for you. Question 3 requires you to analyze the data and provide insights.',
                'message_type' => 'text',
            ],
            [
                'content' => 'Thank you for the clarification!',
                'message_type' => 'text',
            ],
            [
                'content' => 'You\'re welcome! Let me know if you need any further assistance.',
                'message_type' => 'text',
            ],
        ];

        // Create messages between lecturers and students
        foreach ($lecturers as $lecturer) {
            foreach ($students as $index => $student) {
                // Create a conversation with sample messages
                foreach ($sampleMessages as $messageIndex => $messageData) {
                    $isFromLecturer = $messageIndex % 2 === 0;
                    
                    Message::create([
                        'content' => $messageData['content'],
                        'message_type' => $messageData['message_type'],
                        'sender_id' => $isFromLecturer ? $lecturer->id : $student->id,
                        'receiver_id' => $isFromLecturer ? $student->id : $lecturer->id,
                        'is_read' => $messageIndex < count($sampleMessages) - 1, // Last message is unread
                        'read_at' => $messageIndex < count($sampleMessages) - 1 ? now() : null,
                        'created_at' => now()->subDays(rand(1, 7))->subHours(rand(1, 12)),
                        'updated_at' => now()->subDays(rand(1, 7))->subHours(rand(1, 12)),
                    ]);
                }
            }
        }

        $this->command->info('Sample messages created successfully!');
    }
}
