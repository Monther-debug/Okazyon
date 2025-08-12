<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FcmToken>
 */
class FcmTokenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'token' => 'APA91b' . Str::random(134), // Realistic FCM token format
            'device_id' => fake()->uuid(),
            'device_type' => fake()->randomElement(['android', 'ios']),
        ];
    }

    /**
     * Create Android device tokens
     */
    public function android(): static
    {
        return $this->state(fn(array $attributes) => [
            'device_type' => 'android',
            'device_id' => 'android_' . fake()->uuid(),
        ]);
    }

    /**
     * Create iOS device tokens
     */
    public function ios(): static
    {
        return $this->state(fn(array $attributes) => [
            'device_type' => 'ios',
            'device_id' => 'ios_' . fake()->uuid(),
        ]);
    }

    /**
     * Create token for specific user
     */
    public function forUser(User $user): static
    {
        return $this->state(fn(array $attributes) => [
            'user_id' => $user->id,
        ]);
    }
}
