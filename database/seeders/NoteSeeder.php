<?php

namespace Database\Seeders;

use App\Models\Note;
use App\Models\User;
use App\Models\Module;
use Illuminate\Database\Seeder;

class NoteSeeder extends Seeder
{
    public function run(): void
    {
        $students = User::where('role', 'student')->get();
        $modules  = Module::all();

        $titles = [
            'Complete Lecture Notes',
            'Exam Revision Pack',
            'Past Paper Answers',
            'Short Notes & Summaries',
            'Final Exam Preparation Guide',
            'Important Questions & Answers',
        ];

        $descriptions = [
            'Well-structured notes based on lectures.',
            'Useful for exams and assignments.',
            'Compiled from lectures and textbooks.',
        ];

        $previewUrls = [
            'https://picsum.photos/seed/study1/600/400',
            'https://picsum.photos/seed/study2/600/400',
            'https://picsum.photos/seed/study3/600/400',
            'https://picsum.photos/seed/study4/600/400',
            'https://picsum.photos/seed/study5/600/400',
        ];

        foreach ($modules as $module) {
            foreach ($students->random(min(3, $students->count())) as $student) {

                $note = Note::create([
                    'user_id'     => $student->id,
                    'module_id'   => $module->id,
                    'title'       => $module->title . ' – ' . fake()->randomElement($titles),
                    'description' => fake()->randomElement($descriptions),
                    'price'       => fake()->randomElement([500, 1000, 1500, 2000]),
                    'status'      => 'approved',
                ]);

                // Attach 2–3 preview images from URLs
                collect($previewUrls)
                    ->shuffle()
                    ->take(rand(2, 3))
                    ->each(function ($url) use ($note) {
                        $note->addMediaFromUrl($url)
                            ->toMediaCollection('previews');
                    });
            }
        }
    }
}
