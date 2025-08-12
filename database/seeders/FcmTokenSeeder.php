<?php

namespace Database\Seeders;

use App\Models\FcmToken;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FcmTokenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('type', 'user')->get();

        $deviceTypes = ['android', 'ios'];

        foreach ($users->take(10) as $index => $user) {
            // Create 1-2 FCM tokens per user (some users have multiple devices)
            $tokenCount = rand(1, 2);

            for ($i = 0; $i < $tokenCount; $i++) {
                FcmToken::create([
                    'user_id' => $user->id,
                    'token' => $this->generateFakeToken(),
                    'device_id' => 'device_' . $user->id . '_' . $i,
                    'device_type' => $deviceTypes[array_rand($deviceTypes)],
                ]);
            }
        }

        $this->command->info('Created FCM tokens for ' . min(10, $users->count()) . ' users');
        $this->command->info('Total: ' . FcmToken::count() . ' FCM tokens');
    }

    /**
     * Generate a fake FCM token for testing
     */
    private function generateFakeToken(): string
    {
        // Generate a realistic looking FCM token
        $parts = [
            Str::random(11),
            'APA91b' . Str::random(134), // FCM tokens usually start with APA91b
        ];

        return $parts[1];
    }
}
