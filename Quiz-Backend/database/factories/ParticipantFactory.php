<?php

namespace Database\Factories;

use App\Models\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParticipantFactory extends Factory
{
    protected $model = Participant::class;

    public function definition()
    {
        return [
            'email' => $this->faker->unique()->safeEmail(), // Generates a unique email
            'fullName' => $this->faker->name(), // Generates a random name
            'score' => $this->faker->numberBetween(0, 100),
        ];
    }
}
