<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReactionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('reactions')->insert([
            ['id' => 1, 'uuid' => '👍', 'name' => 'like'],
            ['id' => 2, 'uuid' => '❤️', 'name' => 'love'],
            ['id' => 3, 'uuid' => '😂', 'name' => 'haha'],
            ['id' => 4, 'uuid' => '😮', 'name' => 'wow'],
            ['id' => 5, 'uuid' => '😢', 'name' => 'sad'],
            ['id' => 6, 'uuid' => '😡', 'name' => 'angry'],
        ]);
    }
}
