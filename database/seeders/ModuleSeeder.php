<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        Module::insert([
            // Computing – Level 4
            ['level_id' => 1, 'title' => 'Digital Technologies', 'status' => true],
            ['level_id' => 1, 'title' => 'Networking Concepts', 'status' => true],

            // Computing – Level 5
            ['level_id' => 2, 'title' => 'Server-side Programming', 'status' => true],
            ['level_id' => 2, 'title' => 'Mobile App Development', 'status' => true],

            // Computing – Level 6
            ['level_id' => 3, 'title' => 'Final Year Project', 'status' => true],
            ['level_id' => 3, 'title' => 'Emerging Technologies', 'status' => true],

            // Business – Level 4
            ['level_id' => 4, 'title' => 'Principles of Management', 'status' => true],
            ['level_id' => 4, 'title' => 'Business Accounting', 'status' => true],

            // Business – Level 5
            ['level_id' => 5, 'title' => 'Marketing Management', 'status' => true],
            ['level_id' => 5, 'title' => 'Business Analytics', 'status' => true],

            // Business – Level 6
            ['level_id' => 6, 'title' => 'Strategic Management', 'status' => true],
            ['level_id' => 6, 'title' => 'Entrepreneurship', 'status' => true],

            // Law – Level 4
            ['level_id' => 7, 'title' => 'Introduction to Law', 'status' => true],
            ['level_id' => 7, 'title' => 'Legal Systems', 'status' => true],

            // Law – Level 5
            ['level_id' => 8, 'title' => 'Contract Law', 'status' => true],
            ['level_id' => 8, 'title' => 'Criminal Law', 'status' => true],

            // Law – Level 6
            ['level_id' => 9, 'title' => 'Commercial Law', 'status' => true],
            ['level_id' => 9, 'title' => 'Legal Research Project', 'status' => true],
        ]);
    }
}
