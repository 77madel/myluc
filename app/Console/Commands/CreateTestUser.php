<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateTestUser extends Command
{
    protected $signature = 'user:create-test';
    protected $description = 'Create a test user for webinars';

    public function handle()
    {
        $this->info('Creating test user...');

        // Check if user already exists
        $existingUser = DB::table('users')->where('email', 'instructor@test.com')->first();

        if ($existingUser) {
            $this->info("User already exists with ID: {$existingUser->id}");
            return $existingUser->id;
        }

        // Create user with standard Laravel fields
        $userId = DB::table('users')->insertGetId([
            'name' => 'Test Instructor',
            'email' => 'instructor@test.com',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->info("User created with ID: {$userId}");
        return $userId;
    }
}
