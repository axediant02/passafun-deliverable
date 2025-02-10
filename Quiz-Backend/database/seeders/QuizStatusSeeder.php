<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuizStatusSeeder extends Seeder
{
    public function run()
    {
        DB::table('quiz_status')->insert([
            ['status' => 'Unpublished'],
            ['status' => 'Published'],
            ['status' => 'Archived'],
        ]);
    }
}
