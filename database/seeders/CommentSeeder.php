<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\User;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all posts
        $posts = Post::all();
        
        if ($posts->isEmpty()) {
            $this->command->info('No posts found. Please run PostSeeder first.');
            return;
        }

        // Get users (both lecturers and students)
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->info('No users found. Please run UserSeeder first.');
            return;
        }

        $sampleComments = [
            'Great post! Looking forward to joining the class.',
            'This is very helpful information. Thank you!',
            'I have a question about this topic.',
            'Excellent explanation!',
            'Can you provide more details about this?',
            'This is exactly what I needed to know.',
            'Thanks for sharing this valuable information.',
            'I\'m excited to learn more about this subject.',
            'This will be very useful for my studies.',
            'Great initiative! Count me in.',
            'I appreciate the effort you put into this.',
            'This is a fantastic opportunity for students.',
            'Looking forward to the next session!',
            'Thank you for the clear explanation.',
            'This is very well organized.',
        ];

        foreach ($posts as $post) {
            // Add 2-5 random comments to each post
            $numComments = rand(2, 5);
            
            for ($i = 0; $i < $numComments; $i++) {
                $randomUser = $users->random();
                $randomComment = $sampleComments[array_rand($sampleComments)];
                
                PostComment::create([
                    'post_id' => $post->id,
                    'user_id' => $randomUser->id,
                    'content' => $randomComment,
                ]);
            }
            
            // Update the post's comment count
            $post->update([
                'comments_count' => $post->comments()->count()
            ]);
        }

        $this->command->info('Sample comments created successfully!');
    }
} 