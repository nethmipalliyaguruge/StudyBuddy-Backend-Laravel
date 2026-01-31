<?php

namespace Database\Seeders;

use App\Models\Note;
use App\Models\User;
use Illuminate\Database\Seeder;

class NoteSeeder extends Seeder
{
    public function run(): void
    {
        $students = User::where('role', 'student')->get();

        if ($students->isEmpty()) {
            return;
        }

        $notes = [
            // COMPUTING - Digital Technologies (module_id: 1)
            [
                'module_id' => 1,
                'title' => 'Digital Technologies Guide',
                'description' => 'Notes on modern digital systems, hardware, and software.',
                'price' => 3000,
            ],

            // COMPUTING - Networking Concepts and Cyber Security (module_id: 2)
            [
                'module_id' => 2,
                'title' => 'Networking Security Notes',
                'description' => 'Covers firewalls, VPNs, IDS/IPS, secure protocols, and cybersecurity best practices for networks.',
                'price' => 2800,
            ],
            [
                'module_id' => 2,
                'title' => 'Advanced Routing & Switching Guide',
                'description' => 'OSPF, EIGRP, VLANs, STP, and inter-VLAN routing with examples.',
                'price' => 1460,
            ],
            [
                'module_id' => 2,
                'title' => 'Networking Fundamentals',
                'description' => 'OSI model, TCP/IP, routing basics, and practical labs.',
                'price' => 575,
            ],
            [
                'module_id' => 2,
                'title' => 'Networking Cheatsheet (Layer Models)',
                'description' => 'OSI vs TCP/IP, common ports, and subnetting tricks.',
                'price' => 4200,
            ],

            // COMPUTING - Web Development and Operating Systems (module_id: 4)
            [
                'module_id' => 4,
                'title' => 'Web Forms & Validation Checklist',
                'description' => 'Client & server validation, accessibility notes, and security basics.',
                'price' => 3600,
            ],
            [
                'module_id' => 4,
                'title' => 'Web Development Module Notes',
                'description' => 'Advanced data structures, explanations, code examples, and practice problems.',
                'price' => 500,
            ],
            [
                'module_id' => 4,
                'title' => 'WDOS – Web Development & Operating Systems Notes',
                'description' => 'HTML, CSS, JavaScript, and operating system fundamentals.',
                'price' => 750,
            ],

            // COMPUTING - Server-side Programming (module_id: 6)
            [
                'module_id' => 6,
                'title' => 'Server-side Programming',
                'description' => 'PHP, Node.js basics, and server concepts with examples.',
                'price' => 4200,
            ],
            [
                'module_id' => 6,
                'title' => 'Server-side Patterns (PHP & Node)',
                'description' => 'MVC, routing, middleware, and authentication patterns.',
                'price' => 4800,
            ],
            [
                'module_id' => 6,
                'title' => 'Server-side Authentication Techniques',
                'description' => 'JWT, OAuth2, sessions vs tokens with code samples.',
                'price' => 3500,
            ],

            // COMPUTING - Mobile App Development (module_id: 7)
            [
                'module_id' => 7,
                'title' => 'Mobile App Development Basics',
                'description' => 'Step-by-step guide to building Android apps with Java and XML.',
                'price' => 520,
            ],
            [
                'module_id' => 7,
                'title' => 'Mobile App UI Patterns (Slides)',
                'description' => 'Android UI patterns with screenshots and code snippets.',
                'price' => 5600,
            ],

            // COMPUTING - Database and Data Structures (module_id: 8)
            [
                'module_id' => 8,
                'title' => 'Database Systems Guide',
                'description' => 'SQL and database design principles with examples.',
                'price' => 600,
            ],
            [
                'module_id' => 8,
                'title' => 'Database Design & ER Diagrams',
                'description' => 'Normalization, ERD creation, and case studies.',
                'price' => 4800,
            ],
            [
                'module_id' => 8,
                'title' => 'Data Structures Q&A Pack',
                'description' => 'Q&A on arrays, stacks, queues, trees, and graphs.',
                'price' => 390,
            ],
            [
                'module_id' => 8,
                'title' => 'Advanced SQL & Database Optimization',
                'description' => 'Indexing, query optimization, transactions, and stored procedures.',
                'price' => 2000,
            ],
            [
                'module_id' => 8,
                'title' => 'DDS – Database & Data Structures Notes',
                'description' => 'Arrays, linked lists, stacks, queues, trees, graphs, and RDBMS concepts.',
                'price' => 800,
            ],

            // COMPUTING - Mobile App Development Level 6 (module_id: 11)
            [
                'module_id' => 11,
                'title' => 'Mobile App UI/UX Principles',
                'description' => 'UI/UX guidelines for Android & iOS with wireframes and prototypes.',
                'price' => 386,
            ],

            // COMPUTING - Emerging Technologies (module_id: 12)
            [
                'module_id' => 12,
                'title' => 'Emerging Technologies',
                'description' => 'Notes on AI, blockchain, and cloud computing.',
                'price' => 675,
            ],

            // BUSINESS - Foundations of Management (module_id: 13)
            [
                'module_id' => 13,
                'title' => 'Foundations of Management Summary',
                'description' => 'Leadership styles, motivation theories, and organizational structures.',
                'price' => 440,
            ],

            // BUSINESS - Marketing in the Business Environment (module_id: 15)
            [
                'module_id' => 15,
                'title' => 'Marketing Principles – Key Models',
                'description' => 'STP, 4Ps/7Ps, BCG, Ansoff with examples.',
                'price' => 500,
            ],

            // BUSINESS - Operations Management (module_id: 17)
            [
                'module_id' => 17,
                'title' => 'Operations Management Toolkit',
                'description' => 'Process maps, capacity planning, lean tools, and examples.',
                'price' => 610,
            ],

            // BUSINESS - Managing Equality, Diversity, and Inclusion (module_id: 19)
            [
                'module_id' => 19,
                'title' => 'Equality, Diversity & Inclusion Toolkit',
                'description' => 'Workplace diversity management with policies and case studies.',
                'price' => 450,
            ],

            // BUSINESS - Operations Management Level 6 (module_id: 21)
            [
                'module_id' => 21,
                'title' => 'Strategic Mgmt – Global Case Deck',
                'description' => 'Case summaries with SWOT/PESTLE and strategy options.',
                'price' => 700,
            ],

            // LAW - Contract Law (module_id: 27)
            [
                'module_id' => 27,
                'title' => 'Contract Law Case Studies',
                'description' => 'Sri Lankan and UK case law precedents.',
                'price' => 600,
            ],
            [
                'module_id' => 27,
                'title' => 'Contract Law: Remedies & Cases',
                'description' => 'Breach types, damages, and landmark case digests.',
                'price' => 650,
            ],

            // LAW - Constitutional Law (module_id: 28)
            [
                'module_id' => 28,
                'title' => 'Constitutional Law Notes',
                'description' => 'Key constitutional principles with past paper answers.',
                'price' => 550,
            ],
            [
                'module_id' => 28,
                'title' => 'Constitutional Law Short Notes',
                'description' => 'Separation of powers, FR, judicial review, and concise essays.',
                'price' => 520,
            ],

            // LAW - Criminal Law (module_id: 30)
            [
                'module_id' => 30,
                'title' => 'Criminal Law Elements (DOCX)',
                'description' => 'Actus reus, mens rea, offences, and defenses with examples.',
                'price' => 500,
            ],
        ];

        $previewUrls = [
            // Study materials and notes images from Unsplash
            'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=600&h=400&fit=crop', // Notes on desk
            'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=600&h=400&fit=crop', // Study desk with laptop
            'https://images.unsplash.com/photo-1471107340929-a87cd0f5b5f3?w=600&h=400&fit=crop', // Notebook and pen
            'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=600&h=400&fit=crop', // Education books
            'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?w=600&h=400&fit=crop', // Stack of books
            'https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?w=600&h=400&fit=crop', // Open book with notes
            'https://images.unsplash.com/photo-1532012197267-da84d127e765?w=600&h=400&fit=crop', // Open textbook
            'https://images.unsplash.com/photo-1488190211105-8b0e65b80b4e?w=600&h=400&fit=crop', // Writing notes
            'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=600&h=400&fit=crop', // Digital learning
            'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=600&h=400&fit=crop', // Business documents
        ];

        // Sample document files (PDF, DOCX)
        $documentUrls = [
            'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf',
            'https://calibre-ebook.com/downloads/demos/demo.docx',
            'https://filesamples.com/samples/document/docx/sample2.docx',
            'https://filesamples.com/samples/document/pdf/sample2.pdf',
            'https://filesamples.com/samples/document/pdf/sample3.pdf',
        ];

        foreach ($notes as $index => $noteData) {
            // Distribute notes among students
            $student = $students[$index % $students->count()];

            $note = Note::create([
                'user_id' => $student->id,
                'module_id' => $noteData['module_id'],
                'title' => $noteData['title'],
                'description' => $noteData['description'],
                'price' => $noteData['price'],
                'status' => 'approved',
            ]);

            // Attach downloadable document file
            $documentUrl = $documentUrls[$index % count($documentUrls)];
            $note->addMediaFromUrl($documentUrl)
                ->toMediaCollection('note_file');

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
