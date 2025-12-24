<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            [
                'code' => 'CS101',
                'name' => 'Introduction to Computer Science',
                'description' => 'Fundamental concepts of computer science, programming basics, and problem-solving techniques.',
                'base_price' => 50000.00,
                'status' => 'active',
            ],
            [
                'code' => 'MATH201',
                'name' => 'Advanced Mathematics',
                'description' => 'Calculus, algebra, and mathematical analysis for advanced students.',
                'base_price' => 45000.00,
                'status' => 'active',
            ],
            [
                'code' => 'ENG101',
                'name' => 'English Language and Literature',
                'description' => 'Comprehensive English language skills, grammar, and literature analysis.',
                'base_price' => 40000.00,
                'status' => 'active',
            ],
            [
                'code' => 'PHY301',
                'name' => 'Physics Fundamentals',
                'description' => 'Mechanics, thermodynamics, electromagnetism, and modern physics.',
                'base_price' => 55000.00,
                'status' => 'active',
            ],
            [
                'code' => 'CHEM201',
                'name' => 'Chemistry Principles',
                'description' => 'Organic and inorganic chemistry, chemical reactions, and laboratory techniques.',
                'base_price' => 48000.00,
                'status' => 'active',
            ],
            [
                'code' => 'BIO101',
                'name' => 'Biology Basics',
                'description' => 'Cell biology, genetics, ecology, and human anatomy.',
                'base_price' => 42000.00,
                'status' => 'active',
            ],
            [
                'code' => 'ECON201',
                'name' => 'Economics and Finance',
                'description' => 'Microeconomics, macroeconomics, and financial management principles.',
                'base_price' => 47000.00,
                'status' => 'active',
            ],
            [
                'code' => 'HIST101',
                'name' => 'World History',
                'description' => 'Historical events, civilizations, and their impact on modern society.',
                'base_price' => 38000.00,
                'status' => 'active',
            ],
            [
                'code' => 'ART201',
                'name' => 'Visual Arts and Design',
                'description' => 'Drawing, painting, digital art, and design principles.',
                'base_price' => 44000.00,
                'status' => 'active',
            ],
            [
                'code' => 'MUS101',
                'name' => 'Music Theory and Practice',
                'description' => 'Music theory, composition, and practical instrument training.',
                'base_price' => 46000.00,
                'status' => 'active',
            ],
            [
                'code' => 'BUS301',
                'name' => 'Business Administration',
                'description' => 'Business management, entrepreneurship, and organizational behavior.',
                'base_price' => 52000.00,
                'status' => 'active',
            ],
            [
                'code' => 'PSY201',
                'name' => 'Psychology Fundamentals',
                'description' => 'Introduction to psychological principles, behavior, and mental processes.',
                'base_price' => 43000.00,
                'status' => 'active',
            ],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}
