<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminRoleSeeder extends Seeder
{
   
    public function run(): void
    {

        DB::table('admin_roles')->insert([
            ['role' => 'admin'],
            ['role' => 'viewer'],
        ]);
    }
}
