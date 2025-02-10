<?php

namespace Database\Seeders\QuizSamples;

use App\Models\Choice;
use App\Models\GetResultPage;
use App\Models\LandingPage;
use App\Models\MechanicPage;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\Result;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestQuizzesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Number of quizzes to create
        $numberOfQuizzes = 38;
        $currentTime = now();  // Get current timestamp

        for ($quizCount = 0; $quizCount < $numberOfQuizzes; $quizCount++) {
            // Set timestamp for this iteration
            $timestamp = $currentTime->copy()->subSeconds($quizCount);

            // Quiz type combinations for more interesting titles
            $quizTypes = [
                'Leader' => [
                    'prefix' => ['Natural', 'Business', 'Team', 'Visionary', 'Strategic'],
                    'style' => ['Leadership Style', 'Management Approach', 'Leadership Personality', 'Leadership Potential', 'Leadership Archetype']
                ],
                'Personality' => [
                    'prefix' => ['True', 'Hidden', 'Core', 'Inner', 'Authentic'],
                    'style' => ['Personality Type', 'Character Traits', 'Personal Style', 'Personality Profile', 'Character Type']
                ],
                'Career' => [
                    'prefix' => ['Ideal', 'Perfect', 'Dream', 'Best-Fit', 'Optimal'],
                    'style' => ['Career Path', 'Professional Direction', 'Career Destiny', 'Professional Role', 'Career Match']
                ],
                'Creativity' => [
                    'prefix' => ['Creative', 'Artistic', 'Innovative', 'Imaginative', 'Expressive'],
                    'style' => ['Creative Style', 'Artistic Nature', 'Creative Approach', 'Artist Type', 'Creative Spirit']
                ],
                'Intelligence' => [
                    'prefix' => ['Dominant', 'Primary', 'Natural', 'Core', 'Unique'],
                    'style' => ['Intelligence Type', 'Learning Style', 'Thinking Pattern', 'Mental Approach', 'Cognitive Style']
                ]
            ];

            $quizType = fake()->randomElement(array_keys($quizTypes));
            $prefix = fake()->randomElement($quizTypes[$quizType]['prefix']);
            $style = fake()->randomElement($quizTypes[$quizType]['style']);

            // Create Landing Page with timestamp
            $landingPage = LandingPage::create([
                'background_image' => 'TEST.jpg',
                'landing_page_image' => 'TEST.jpg',
                'header' => "Discover Your {$prefix} {$style}",
                'sub_header' => fake()->randomElement([
                    "Uncover the unique way you approach life and work",
                    "Learn what makes you truly special in just a few minutes",
                    "Gain insights into your natural talents and potential",
                    "Explore your distinctive characteristics and strengths",
                    "Understand yourself better through this revealing quiz"
                ]),
                'button_text' => fake()->randomElement(['Start Quiz', 'Begin Journey', 'Take the Quiz', 'Discover Yourself', 'Let\'s Begin']),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);

            // Create Mechanic Page with Instructions
            // $mechanicPage = MechanicPage::create([
            //     'header' => fake()->randomElement(['How to Play', 'Quiz Instructions', 'Before You Start', 'Quick Guide']),
            //     'button_text' => fake()->randomElement(['Begin', 'Start Now', 'Let\'s Go', 'Continue'])
            // ]);

            // Create Quiz using factory with timestamp
            $quiz = \Database\Factories\ForTesting\QuizFactory::new()->create([
                'landing_page_id' => $landingPage->id,
                // 'mechanic_page_id' => $mechanicPage->id,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);

            // Create Get Result Page
            GetResultPage::create([
                'quiz_id' => $quiz->id,
                'header' => fake()->randomElement([
                    'Ready to See Your Results?',
                    'Discover Your Profile!',
                    'Your Results Are Ready!',
                    'Let\'s Reveal Your Results!'
                ]),
                'button_text' => fake()->randomElement(['View My Results', 'Show Results', 'Reveal Now', 'See My Profile']),
                'background_image' => 'TEST.jpg',
                'get_result_page_image' => 'TEST.jpg'
            ])->inputForms()->createMany([
                ['type' => 'text', 'label' => 'Name', 'is_required' => true],
                ['type' => 'email', 'label' => 'Email', 'is_required' => true],
            ]);

            // Random number of questions per quiz (between 5 and 10)
            $numberOfQuestions = fake()->numberBetween(5, 10);

            // Questions and choices arrays for more realistic content
            $questionTemplates = [
                'How do you typically handle %s?',
                'What\'s your preferred approach to %s?',
                'When faced with %s, what do you do?',
                'Which best describes your reaction to %s?',
                'How would others describe your %s?'
            ];

            $situations = [
                'challenging situations',
                'decision-making',
                'working in teams',
                'learning new skills',
                'problem-solving',
                'time management',
                'communication',
                'planning',
                'stress',
                'change'
            ];

            // Create Questions and Choices
            for ($i = 1; $i <= $numberOfQuestions; $i++) {
                $question = QuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question_type_id' => fake()->numberBetween(1, 2),
                    'question_text' => sprintf(
                        fake()->randomElement($questionTemplates),
                        fake()->randomElement($situations)
                    ),
                    'question_order' => $i,
                ]);

                // Create realistic choices based on question context
                $choiceTemplates = [
                    ['Take immediate action', 'Analyze carefully', 'Seek advice', 'Go with instinct'],
                    ['Very organized', 'Somewhat structured', 'Flexible', 'Spontaneous'],
                    ['Lead the way', 'Support others', 'Collaborate', 'Observe and adapt'],
                    ['Traditional approach', 'Innovative method', 'Balanced style', 'Unique solution']
                ];

                $choices = fake()->randomElement($choiceTemplates);
                shuffle($choices);

                $numberOfChoices = fake()->numberBetween(2, 4);
                for ($j = 0; $j < $numberOfChoices; $j++) {
                    Choice::create([
                        'question_id' => $question->id,
                        'choice_text' => $choices[$j],
                        'points' => $j + 1,
                        'is_correct' => $j === 0
                    ]);
                }
            }

            // Create Results with meaningful content
            $resultTemplates = [
                [
                    'header' => 'The Strategic Thinker',
                    'description' => 'You excel at analyzing situations and creating long-term plans. Your methodical approach and attention to detail make you a valuable problem solver.'
                ],
                [
                    'header' => 'The Natural Leader',
                    'description' => 'You have a natural ability to inspire and guide others. Your confidence and decision-making skills make you an effective leader in any situation.'
                ],
                [
                    'header' => 'The Creative Innovator',
                    'description' => 'Your unique perspective and creative thinking help you find novel solutions to challenges. You\'re not afraid to think outside the box.'
                ],
                [
                    'header' => 'The Collaborative Partner',
                    'description' => 'You thrive in team environments and excel at building relationships. Your emotional intelligence and communication skills are your greatest strengths.'
                ],
                [
                    'header' => 'The Adaptable Pioneer',
                    'description' => 'Your flexibility and quick learning ability make you excellent at handling change. You\'re always ready to explore new opportunities.'
                ]
            ];

            // Create Results (3-5 possible results per quiz)
            $numberOfResults = fake()->numberBetween(3, 5);
            $maxPoints = $numberOfQuestions * 4; // Maximum possible points
            $pointsPerResult = ceil($maxPoints / $numberOfResults);

            for ($i = 0; $i < $numberOfResults; $i++) {
                $minPoints = $i * $pointsPerResult;
                $maxPoints = ($i === $numberOfResults - 1)
                    ? $maxPoints
                    : ($i + 1) * $pointsPerResult - 1;

                Result::create([
                    'quiz_id' => $quiz->id,
                    'header' => fake()->words(3, true),
                    'description' => fake()->paragraph(),
                    'image' => 'TEST.jpg',
                    'min_points' => $minPoints,
                    'max_points' => $maxPoints
                ]);
            }
        }
    }
}
