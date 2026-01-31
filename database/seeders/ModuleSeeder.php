<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        Module::insert([
            // School of Computing – Level 4 (level_id: 1)
            ['level_id' => 1, 'title' => 'Digital Technologies', 'status' => true],
            ['level_id' => 1, 'title' => 'Networking Concepts and Cyber Security', 'status' => true],
            ['level_id' => 1, 'title' => 'Software Development and Application Modelling', 'status' => true],
            ['level_id' => 1, 'title' => 'Web Development and Operating Systems', 'status' => true],

            // School of Computing – Level 5 (level_id: 2)
            ['level_id' => 2, 'title' => 'Commercial Computing', 'status' => true],
            ['level_id' => 2, 'title' => 'Server-side Programming', 'status' => true],
            ['level_id' => 2, 'title' => 'Mobile App Development', 'status' => true],
            ['level_id' => 2, 'title' => 'Database and Data Structures', 'status' => true],

            // School of Computing – Level 6 (level_id: 3)
            ['level_id' => 3, 'title' => 'Final Year Project', 'status' => true],
            ['level_id' => 3, 'title' => 'Server-side Programming', 'status' => true],
            ['level_id' => 3, 'title' => 'Mobile App Development', 'status' => true],
            ['level_id' => 3, 'title' => 'Emerging Technologies', 'status' => true],

            // School of Business – Level 4 (level_id: 4)
            ['level_id' => 4, 'title' => 'Foundations of Management', 'status' => true],
            ['level_id' => 4, 'title' => 'Introduction to Management Accounting', 'status' => true],
            ['level_id' => 4, 'title' => 'Marketing in the Business Environment', 'status' => true],
            ['level_id' => 4, 'title' => 'Business Law', 'status' => true],

            // School of Business – Level 5 (level_id: 5)
            ['level_id' => 5, 'title' => 'Operations Management', 'status' => true],
            ['level_id' => 5, 'title' => 'Sustainable Business Development', 'status' => true],
            ['level_id' => 5, 'title' => 'Managing Equality, Diversity, and Inclusion', 'status' => true],
            ['level_id' => 5, 'title' => 'Elective Modules', 'status' => true],

            // School of Business – Level 6 (level_id: 6)
            ['level_id' => 6, 'title' => 'Operations Management', 'status' => true],
            ['level_id' => 6, 'title' => 'Sustainable Business Development', 'status' => true],
            ['level_id' => 6, 'title' => 'Managing Equality, Diversity, and Inclusion', 'status' => true],
            ['level_id' => 6, 'title' => 'Elective Modules', 'status' => true],

            // School of Law – Level 4 (level_id: 7)
            ['level_id' => 7, 'title' => 'Legal English', 'status' => true],
            ['level_id' => 7, 'title' => 'English Legal System', 'status' => true],
            ['level_id' => 7, 'title' => 'Contract Law', 'status' => true],
            ['level_id' => 7, 'title' => 'Constitutional Law', 'status' => true],
            ['level_id' => 7, 'title' => 'Tort Law', 'status' => true],

            // School of Law – Level 5 (level_id: 8)
            ['level_id' => 8, 'title' => 'Criminal Law', 'status' => true],
            ['level_id' => 8, 'title' => 'Property Law and Application', 'status' => true],
            ['level_id' => 8, 'title' => 'Administrative Law', 'status' => true],
            ['level_id' => 8, 'title' => 'European Union Law', 'status' => true],

            // School of Law – Level 6 (level_id: 9)
            ['level_id' => 9, 'title' => 'Law of Trusts and Equitable Remedies', 'status' => true],
            ['level_id' => 9, 'title' => 'Dissertation', 'status' => true],
            ['level_id' => 9, 'title' => 'Optional Modules (e.g., Cyber Law, Company & Commercial Law, Employment Law)', 'status' => true],
        ]);
    }
}
