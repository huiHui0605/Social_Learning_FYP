<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GroupChat;
use App\Models\GroupMessage;
use App\Models\User;

class GroupChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users
        $users = User::all();
        
        if ($users->count() < 2) {
            $this->command->info('Not enough users to create group chats. Please run UserSeeder first.');
            return;
        }

        // Create sample group chats
        $groups = [
            [
                'name' => 'Computer Science Students',
                'description' => 'A group for CS students to discuss programming, algorithms, and projects.',
                'type' => 'public',
                'max_members' => 50,
                'created_by' => $users->first()->id,
            ],
            [
                'name' => 'Study Group - Mathematics',
                'description' => 'Study group for mathematics courses. Share notes and help each other.',
                'type' => 'public',
                'max_members' => 30,
                'created_by' => $users->first()->id,
            ],
            [
                'name' => 'Project Collaboration',
                'description' => 'Group for students working on group projects.',
                'type' => 'private',
                'max_members' => 20,
                'created_by' => $users->first()->id,
            ],
        ];

        foreach ($groups as $groupData) {
            $group = GroupChat::create($groupData);
            
            // Add members to the group
            $memberCount = rand(3, min(8, $users->count()));
            $selectedUsers = $users->random($memberCount);
            
            foreach ($selectedUsers as $index => $user) {
                $role = $index === 0 ? 'admin' : 'member';
                $group->addMember($user, $role);
            }

            // Create sample messages
            $messages = [
                'Hello everyone! Welcome to the group.',
                'Does anyone have the assignment for this week?',
                'I found some great resources for our project.',
                'Can we schedule a meeting for tomorrow?',
                'Great work on the presentation!',
                'Don\'t forget about the deadline next week.',
                'I have a question about the latest topic.',
                'Thanks for sharing those notes!',
            ];

            foreach ($messages as $messageText) {
                $sender = $selectedUsers->random();
                GroupMessage::create([
                    'group_chat_id' => $group->id,
                    'sender_id' => $sender->id,
                    'content' => $messageText,
                    'message_type' => 'text',
                ]);
            }
        }

        $this->command->info('Group chats and messages created successfully!');
    }
}
