<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Instructor
        $instructor = User::create([
            'name' => 'Instructor User',
            'email' => 'instructor@example.com',
            'password' => Hash::make('password'),
        ]);
        $instructor->assignRole('Instructor');

        // Create Students
        $student1 = User::create([
            'name' => 'Student User 1',
            'email' => 'student1@example.com',
            'password' => Hash::make('password'),
        ]);
        $student1->assignRole('Student');

        $student2 = User::create([
            'name' => 'Student User 2',
            'email' => 'student2@example.com',
            'password' => Hash::make('password'),
        ]);
        $student2->assignRole('Student');
    }
}
