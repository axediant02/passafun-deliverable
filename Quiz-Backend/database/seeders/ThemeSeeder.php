<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ThemeSeeder extends Seeder
{
    public function run()
    {
        DB::table('themes')->insert([
            [
                'name' => 'Lavender',
                'main_color' => '#7B2CBF',
                'accent_color' => '#3C096C',
                'button_color' => '#9D4EDD',
                'text_color' => '#10002B',
                'background_type' => 'color',
                'background_value' => '#FFFFFF',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Ocean Depths',
                'main_color' => '#1D3557',
                'accent_color' => '#001F3F',
                'button_color' => '#457B9D',
                'text_color' => '#0A1A2A',
                'background_type' => 'color',
                'background_value' => '#F1FAEE',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Emerald Twilight',
                'main_color' => '#2A9D8F',
                'accent_color' => '#264653',
                'button_color' => '#43AA8B',
                'text_color' => '#0C3A3A',
                'background_type' => 'color',
                'background_value' => '#F4F9F9',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Crimson Flame',
                'main_color' => '#C72C41',
                'accent_color' => '#7A1B2C',
                'button_color' => '#FF6F61',
                'text_color' => '#2C0F12',
                'background_type' => 'color',
                'background_value' => '#FFF5F5',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Emerald Forest',
                'main_color' => '#2ECC71',
                'accent_color' => '#145A32',
                'button_color' => '#27AE60',
                'text_color' => '#0A3D0A',
                'background_type' => 'color',
                'background_value' => '#E8F8F5',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Tropical Breeze',
                'main_color' => '#00BFAE', 
                'accent_color' => '#004D40',
                'button_color' => '#1DE9B6',
                'text_color' => '#00251A',
                'background_type' => 'color',
                'background_value' => '#E0F2F1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Ocean Waves',
                'main_color' => '#0288D1',
                'accent_color' => '#01579B',
                'button_color' => '#03A9F4',
                'text_color' => '#003B6F',
                'background_type' => 'color',
                'background_value' => '#E1F5FE',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
