<?php

namespace Database\Seeders;

use App\Models\User;
use App\Utility\Enums\UserStatusEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin Users
        $admin1 = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'phone_number' => '+1234567890',
            'date_of_birth' => '1990-01-01',
            'gender' => 'male',
            'password' => Hash::make('password123'),
            'type' => 'admin',
            'status' => UserStatusEnum::ACTIVE,
        ]);

        $admin2 = User::create([
            'first_name' => 'Sarah',
            'last_name' => 'Administrator',
            'phone_number' => '+1234567891',
            'date_of_birth' => '1988-05-15',
            'gender' => 'female',
            'password' => Hash::make('password123'),
            'type' => 'admin',
            'status' => UserStatusEnum::ACTIVE,
        ]);

        // Create Regular Users
        $users = [
            [
                'first_name' => 'Ahmed',
                'last_name' => 'Ali',
                'phone_number' => '+1234567892',
                'date_of_birth' => '1995-03-20',
                'gender' => 'male',
                'password' => Hash::make('password123'),
                'type' => 'user',
                'status' => UserStatusEnum::ACTIVE,
            ],
            [
                'first_name' => 'Fatima',
                'last_name' => 'Hassan',
                'phone_number' => '+1234567893',
                'date_of_birth' => '1992-07-10',
                'gender' => 'female',
                'password' => Hash::make('password123'),
                'type' => 'user',
                'status' => UserStatusEnum::ACTIVE,
            ],
            [
                'first_name' => 'Omar',
                'last_name' => 'Mohamed',
                'phone_number' => '+1234567894',
                'date_of_birth' => '1998-11-25',
                'gender' => 'male',
                'password' => Hash::make('password123'),
                'type' => 'user',
                'status' => UserStatusEnum::ACTIVE,
            ],
            [
                'first_name' => 'Aisha',
                'last_name' => 'Ibrahim',
                'phone_number' => '+1234567895',
                'date_of_birth' => '1996-02-14',
                'gender' => 'female',
                'password' => Hash::make('password123'),
                'type' => 'user',
                'status' => UserStatusEnum::ACTIVE,
            ],
            [
                'first_name' => 'Banned',
                'last_name' => 'User',
                'phone_number' => '+1234567896',
                'date_of_birth' => '1990-12-01',
                'gender' => 'male',
                'password' => Hash::make('password123'),
                'type' => 'user',
                'status' => UserStatusEnum::BANNED,
            ],
            [
                'first_name' => 'Inactive',
                'last_name' => 'User',
                'phone_number' => '+1234567897',
                'date_of_birth' => '1994-08-30',
                'gender' => 'female',
                'password' => Hash::make('password123'),
                'type' => 'user',
                'status' => UserStatusEnum::INACTIVE,
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        // Create additional random users using factory
        User::factory(20)->create();

        $this->command->info('Created users:');
        $this->command->info('- 2 Admin users (password: password123)');
        $this->command->info('- 6 Regular users with different statuses (password: password123)');
        $this->command->info('- 20 Random users via factory');
        $this->command->info('Total: ' . User::count() . ' users');
    }
}
