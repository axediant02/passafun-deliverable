<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionTypeSeeder extends Seeder
{
    public function run()
    {
        DB::table('question_types')->insert([
            ['type' => 'Multiple Select Choice'],
            ['type' => 'Single Select Choice'],
            ['type' => 'True/False'],
            ['type' => 'Open Ended'],
            ['type' => 'Rating Scale'],
        ]);
    }
}
