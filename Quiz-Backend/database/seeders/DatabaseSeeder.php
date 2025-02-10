<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use App\Models\Participant;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminRoleSeeder::class,
            AdminSeeder::class,
            ThemeSeeder::class,
            QuizStatusSeeder::class,
            QuestionTypeSeeder::class,
            ImageTypeSeeder::class,
        ]);
    }
}
