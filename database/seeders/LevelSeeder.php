<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        Level::insert([
            // Computing
            ['id' => 1, 'school_id' => 1, 'name' => 'Level 4'],
            ['id' => 2, 'school_id' => 1, 'name' => 'Level 5'],
            ['id' => 3, 'school_id' => 1, 'name' => 'Level 6'],

            // Business
            ['id' => 4, 'school_id' => 2, 'name' => 'Level 4'],
            ['id' => 5, 'school_id' => 2, 'name' => 'Level 5'],
            ['id' => 6, 'school_id' => 2, 'name' => 'Level 6'],

            // Law
            ['id' => 7, 'school_id' => 3, 'name' => 'Level 4'],
            ['id' => 8, 'school_id' => 3, 'name' => 'Level 5'],
            ['id' => 9, 'school_id' => 3, 'name' => 'Level 6'],
        ]);
    }
}
