<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            PostSeeder::class,
            AssessmentSeeder::class,
            FeedbackSeeder::class,
            MessageSeeder::class,
            GroupChatSeeder::class,
        ]);

        // Add messaging relationships seeder for testing
        // Uncomment the line below to ensure all users have proper course relationships
        // $this->call(MessagingSeeder::class);
    }
}
