<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AdminFactory extends Factory
{
    protected $model = Admin::class;

    public function definition()
    {
        return [
            'firstName' => $this->faker->firstName(),  // Generates a random first name
            'lastName' => $this->faker->lastName(),     // Generates a random last name
            'email' => $this->faker->unique()->safeEmail(), // Generates a unique email
            'password' => bcrypt('password'), // Default password, bcrypt for hashing
            'role' => $this->faker->randomElement(['admin', 'superadmin']), // Ensure these match your database constraints
        ];
    }
}
