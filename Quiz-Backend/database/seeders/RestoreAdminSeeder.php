<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RestoreAdminSeeder extends Seeder
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
        ]);
        
    }
}
