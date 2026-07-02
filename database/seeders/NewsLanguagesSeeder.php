<?php

namespace Database\Seeders;

use App\Models\NewsLanguage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NewsLanguagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        NewsLanguage::insert([
           
            [
                'name' => 'Gujarati',
                'code' => 'gu'
            ],
            [
                'name' => 'Marathi',
                'code' => 'mr'
            ],
           
            [
                'name' => 'Tamil',
                'code' => 'ta'
            ],
            [
                'name' => 'Telugu',
                'code' => 'te'
            ],
            [
                'name' => 'Bengali',
                'code' => 'bn'
            ],
            [
                'name' => 'Punjabi',
                'code' => 'pa'
            ],
            [
                'name' => 'Malayalam',
                'code' => 'ml'
            ],
            [
                'name' => 'Kannada',
                'code' => 'kn'
            ],
            [
                'name' => 'Urdu',
                'code' => 'ur'
            ],
            [
                'name' => 'Odia',
                'code' => 'or'
            ],
            [
                'name' => 'Assamese',
                'code' => 'as'
            ],
            [
                'name' => 'Nepali',
                'code' => 'ne'
            ],
        ]);
    }
}
