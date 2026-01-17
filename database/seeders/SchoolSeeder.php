<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        School::insert([
            ['id' => 1, 'name' => 'School of Computing', 'description' => 'Computing related programs'],
            ['id' => 2, 'name' => 'School of Business', 'description' => 'Business related programs'],
            ['id' => 3, 'name' => 'School of Law', 'description' => 'Law related programs'],
        ]);
    }
}
