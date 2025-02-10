<?php

namespace Database\Factories\ForTesting;

use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuizFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Quiz::class;
    public function definition(): array
    {
        $quizTypes = [
            'Leadership' => [
                'prefixes' => ['Natural', 'Business', 'Team', 'Visionary', 'Strategic', 'Executive', 'Transformational'],
                'styles' => ['Style', 'Approach', 'Type', 'Potential', 'DNA', 'Mindset', 'Archetype']
            ],
            'Personality' => [
                'prefixes' => ['True', 'Hidden', 'Core', 'Inner', 'Authentic', 'Real', 'Essential', 'Genuine'],
                'styles' => ['Type', 'Profile', 'Traits', 'Pattern', 'Style', 'Dimensions', 'Framework']
            ],
            'Career' => [
                'prefixes' => ['Ideal', 'Perfect', 'Dream', 'Best-Fit', 'Optimal', 'Future', 'Ultimate'],
                'styles' => ['Path', 'Direction', 'Destiny', 'Match', 'Calling', 'Journey', 'Roadmap']
            ],
            'Creativity' => [
                'prefixes' => ['Creative', 'Artistic', 'Innovative', 'Imaginative', 'Expressive', 'Original', 'Inventive'],
                'styles' => ['Style', 'Nature', 'Approach', 'Identity', 'Spirit', 'Process', 'Method']
            ],
            'Intelligence' => [
                'prefixes' => ['Dominant', 'Primary', 'Natural', 'Core', 'Unique', 'Fundamental', 'Essential'],
                'styles' => ['Type', 'Pattern', 'Profile', 'Framework', 'Structure', 'Model', 'Category']
            ],
            'Communication' => [
                'prefixes' => ['Natural', 'Effective', 'Dynamic', 'Authentic', 'Powerful', 'Strategic', 'Essential'],
                'styles' => ['Style', 'Pattern', 'Approach', 'Method', 'Framework', 'Type', 'Profile']
            ],
            'Learning' => [
                'prefixes' => ['Optimal', 'Natural', 'Primary', 'Core', 'Dominant', 'Essential', 'Key'],
                'styles' => ['Style', 'Method', 'Approach', 'Strategy', 'Pattern', 'Preference', 'Type']
            ]
        ];

        $type = fake()->randomElement(array_keys($quizTypes));
        $prefix = fake()->randomElement($quizTypes[$type]['prefixes']);
        $style = fake()->randomElement($quizTypes[$type]['styles']);

        // Different name formats
        $nameFormats = [
            "{$prefix} {$type} {$style} Quiz",
            "What's Your {$prefix} {$type} {$style}?",
            "Discover Your {$type} {$style}",
            "The {$prefix} {$type} Quiz",
            "{$type} {$style} Assessment"
        ];

        $name = fake()->randomElement($nameFormats);

        $descriptionFormats = [
            "Discover your {$prefix} {$type} {$style} and understand your unique characteristics and potential.",
            "Uncover your natural {$type} {$style} through this insightful assessment.",
            "Explore the depths of your {$prefix} {$type} {$style} with our comprehensive quiz.",
            "Learn what makes your {$type} {$style} unique in this revealing assessment.",
            "Gain valuable insights into your personal {$type} {$style} and leverage your strengths."
        ];

        return [
            'name' => $name,
            'description' => fake()->randomElement($descriptionFormats),
            'thumbnail' => 'TEST.jpg',
            'quiz_status_id' => 2,
            'theme_id' => fake()->numberBetween(1, 5),
            'admin_id' => 3,
            'uid' => 'quiz_' . uniqid(),
        ];
    }
}
