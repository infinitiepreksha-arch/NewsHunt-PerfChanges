<?php

namespace Database\Seeders;

use App\Models\Topic;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TopicsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Topic::insert([
            [
                'id' => 1,
                'name' => 'Sports',
                'slug' => 'sports',
                'logo' => '7255sports.jpg',
                'status' => 'active',
            ],
            [
                'id' => 2,
                'name' => 'Business',
                'slug' => 'business',
                'logo' => '8383business.jpg',
                'status' => 'active',
            ],
            [
                'id' => 3,
                'name' => 'Economy',
                'slug' => 'economy',
                'logo' => '5272Economy.jpg',
                'status' => 'active',
            ],
            [
                'id' => 4,
                'name' => 'Entertainment',
                'slug' => 'entertainment',
                'logo' => '2411Entertaintment.jpg',
                'status' => 'active',
            ],
            [
                'id' => 5,
                'name' => 'Health',
                'slug' => 'health',
                'logo' => '9322Health.jpg',
                'status' => 'active',
            ],
            [
                'id' => 6,
                'name' => 'Politics',
                'slug' => 'politics',
                'logo' => '9528politics.jpg',
                'status' => 'active',
            ],
            [
                'id' => 7,
                'name' => 'Lifestyle',
                'slug' => 'lifestyle',
                'logo' => '2482lifestyle.jpg',
                'status' => 'active',
            ],
            [
                'id' => 8,
                'name' => 'Science',
                'slug' => 'science',
                'logo' => '1788Science.jpg',
                'status' => 'active',
            ],
            [
                'id' => 9,
                'name' => 'Technology',
                'slug' => 'technology',
                'logo' => '5674Technology.jpg',
                'status' => 'active',
            ],
            [
                'id' => 10,
                'name' => 'Weather',
                'slug' => 'weather',
                'logo' => '1823Weather.jpg',
                'status' => 'active',
            ],
            [
                'id' => 11,
                'name' => 'World',
                'slug' => 'world',
                'logo' => '5282world.jpg',
                'status' => 'active',
            ]
        ]);
        
    }
}
