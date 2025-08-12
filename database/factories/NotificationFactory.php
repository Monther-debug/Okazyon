<?php

namespace Database\Factories;

use App\Models\User;
use App\Utility\Enums\NotificationStatusEnum;
use App\Utility\Enums\NotificationTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $targetType = fake()->randomElement(NotificationTypeEnum::cases());
        $targetId = $targetType === NotificationTypeEnum::SPECIFIC_USER
            ? User::where('type', 'user')->inRandomOrder()->first()?->id
            : null;

        return [
            'target_id' => $targetId,
            'en_title' => fake()->sentence(3),
            'ar_title' => 'إشعار: ' . fake()->sentence(2),
            'en_body' => fake()->paragraph(2),
            'ar_body' => 'محتوى الإشعار: ' . fake()->sentence(5),
            'target_type' => $targetType,
            'status' => fake()->randomElement(NotificationStatusEnum::cases()),
            'scheduled_at' => fake()->boolean(30) ? fake()->dateTimeBetween('now', '+1 month') : null,
        ];
    }

    /**
     * Create sent notifications
     */
    public function sent(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => NotificationStatusEnum::SENT,
            'scheduled_at' => null,
        ]);
    }

    /**
     * Create pending notifications
     */
    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => NotificationStatusEnum::PENDING,
            'scheduled_at' => null,
        ]);
    }

    /**
     * Create scheduled notifications
     */
    public function scheduled(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => NotificationStatusEnum::SCHEDULED,
            'scheduled_at' => fake()->dateTimeBetween('now', '+1 week'),
        ]);
    }

    /**
     * Create notifications for all users
     */
    public function forAllUsers(): static
    {
        return $this->state(fn(array $attributes) => [
            'target_type' => NotificationTypeEnum::ALL,
            'target_id' => null,
        ]);
    }

    /**
     * Create notifications for specific user
     */
    public function forSpecificUser($userId = null): static
    {
        return $this->state(fn(array $attributes) => [
            'target_type' => NotificationTypeEnum::SPECIFIC_USER,
            'target_id' => $userId ?? User::where('type', 'user')->inRandomOrder()->first()?->id,
        ]);
    }
}
