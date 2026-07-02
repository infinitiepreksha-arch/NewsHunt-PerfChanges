<?php

namespace Database\Seeders;

use App\Models\Channel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChannelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Channel::insert([
        [
            'country_id' => 0,
            'language_id' => 0,
            'name' => 'Fox News',
            'logo' => '5280fox-logo.png',
            'slug' => 'fox-news',
            'status' => 'active',
        ],
        [
            'country_id' => 0,
            'language_id' => 0,
            'name' => 'Times of India',
            'logo' => '8574TOI-logo.jpg',
            'slug' => 'times-of-india',
            'status' => 'active',
        ],
        [
            'country_id' => 0,
            'language_id' => 0,
            'name' => 'India TV',
            'logo' => '5542india TV',
            'slug' => 'india-tv',
            'status' => 'active',
        ]
    ]);
    }
}
