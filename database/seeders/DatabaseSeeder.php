<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            NotificationSeeder::class,
            FcmTokenSeeder::class,
        ]);

        $this->command->info('🎉 Database seeding completed successfully!');
        $this->command->info('');
        $this->command->info('📊 Summary:');
        $this->command->info('✓ Users seeded with admin and regular accounts');
        $this->command->info('✓ Notifications seeded (general, specific, and scheduled)');
        $this->command->info('✓ FCM tokens seeded for testing push notifications');
        $this->command->info('');
        $this->command->info('🔐 Test Credentials:');
        $this->command->info('Admin: +1234567890 / password: password123');
        $this->command->info('User: +1234567892 / password: password123');
    }
}
