<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        DB::table('admins')->insert([
            [
                'first_name' => 'Passafund',
                'last_name' => 'Administrator',
                'email' => 'admin@passafund.com',
                'password' => Hash::make('gwapo123'),
                'role_id' => '1',
            ],
            [
                'first_name' => 'Passafund',
                'last_name' => 'Visitor',
                'email' => 'visitor@passafund.com',
                'password' => Hash::make('gwapo123'),
                'role_id' => '2',
            ],
        ]);
        
    }
}
