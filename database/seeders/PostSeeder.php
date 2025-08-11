<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\User;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users to create posts for
        $users = User::where('role', 'lecturer')->take(3)->get();
        
        if ($users->isEmpty()) {
            // Create a sample lecturer if none exist
            $user = User::create([
                'name' => 'Mr. Cheng',
                'email' => 'cheng@example.com',
                'password' => bcrypt('password'),
                'role' => 'lecturer',
            ]);
            $users = collect([$user]);
        }

        $samplePosts = [
            [
                'content' => 'All those students who are interested can join our online English classes! We\'ll be covering advanced grammar and conversation skills.',
                'user' => $users->first(),
            ],
            [
                'content' => 'Basic video design Part 1: Planning and Filming. Today we discussed the fundamentals of creating engaging educational content.',
                'user' => $users->count() > 1 ? $users[1] : $users->first(),
            ],
            [
                'content' => 'Great session today with the students! Remember to submit your assignments by Friday. Looking forward to seeing your creative projects.',
                'user' => $users->count() > 2 ? $users[2] : $users->first(),
            ],
        ];

        foreach ($samplePosts as $postData) {
            Post::create([
                'content' => $postData['content'],
                'user_id' => $postData['user']->id,
                'likes_count' => rand(0, 15),
                'comments_count' => rand(0, 8),
                'shares_count' => rand(0, 5),
            ]);
        }
    }
}
