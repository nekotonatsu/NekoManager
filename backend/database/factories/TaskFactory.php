<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'title'       => $this->faker->sentence(3),
            'description' => $this->faker->optional()->paragraph(),
            'due_date'    => $this->faker->boolean() ? now()
                                ->addDays(rand(1, 365))->format('Y-m-d')
                                : null,
            'completed'   => false,
        ];
    }
}