<?php

namespace Database\Factories;

use App\Utility\Enums\UserStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'phone_number' => '+' . fake()->numerify('############'),
            'date_of_birth' => fake()->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d'),
            'gender' => fake()->randomElement(['male', 'female', 'other']),
            'password' => static::$password ??= Hash::make('password'),
            'type' => fake()->randomElement(['user', 'admin']),
            'status' => fake()->randomElement(UserStatusEnum::cases()),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Create active users
     */
    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => UserStatusEnum::ACTIVE,
        ]);
    }

    /**
     * Create admin users
     */
    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'admin',
            'status' => UserStatusEnum::ACTIVE,
        ]);
    }

    /**
     * Create regular users
     */
    public function user(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'user',
            'status' => UserStatusEnum::ACTIVE,
        ]);
    }

    /**
     * Create banned users
     */
    public function banned(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => UserStatusEnum::BANNED,
        ]);
    }
}
