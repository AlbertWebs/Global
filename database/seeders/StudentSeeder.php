<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $students = [
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@student.globalcollege.edu',
                'phone' => '+234 801 234 5678',
                'date_of_birth' => '2005-03-15',
                'address' => '123 Main Street, Lagos, Nigeria',
                'status' => 'active',
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@student.globalcollege.edu',
                'phone' => '+234 802 345 6789',
                'date_of_birth' => '2005-07-22',
                'address' => '456 Oak Avenue, Abuja, Nigeria',
                'status' => 'active',
            ],
            [
                'first_name' => 'Michael',
                'last_name' => 'Johnson',
                'email' => 'michael.johnson@student.globalcollege.edu',
                'phone' => '+234 803 456 7890',
                'date_of_birth' => '2004-11-08',
                'address' => '789 Pine Road, Port Harcourt, Nigeria',
                'status' => 'active',
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Williams',
                'email' => 'sarah.williams@student.globalcollege.edu',
                'phone' => '+234 804 567 8901',
                'date_of_birth' => '2005-01-30',
                'address' => '321 Elm Street, Ibadan, Nigeria',
                'status' => 'active',
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Brown',
                'email' => 'david.brown@student.globalcollege.edu',
                'phone' => '+234 805 678 9012',
                'date_of_birth' => '2004-09-14',
                'address' => '654 Maple Drive, Kano, Nigeria',
                'status' => 'active',
            ],
            [
                'first_name' => 'Emily',
                'last_name' => 'Davis',
                'email' => 'emily.davis@student.globalcollege.edu',
                'phone' => '+234 806 789 0123',
                'date_of_birth' => '2005-05-20',
                'address' => '987 Cedar Lane, Enugu, Nigeria',
                'status' => 'active',
            ],
            [
                'first_name' => 'James',
                'last_name' => 'Miller',
                'email' => 'james.miller@student.globalcollege.edu',
                'phone' => '+234 807 890 1234',
                'date_of_birth' => '2004-12-05',
                'address' => '147 Birch Court, Kaduna, Nigeria',
                'status' => 'active',
            ],
            [
                'first_name' => 'Olivia',
                'last_name' => 'Wilson',
                'email' => 'olivia.wilson@student.globalcollege.edu',
                'phone' => '+234 808 901 2345',
                'date_of_birth' => '2005-08-18',
                'address' => '258 Spruce Way, Benin City, Nigeria',
                'status' => 'active',
            ],
            [
                'first_name' => 'Robert',
                'last_name' => 'Moore',
                'email' => 'robert.moore@student.globalcollege.edu',
                'phone' => '+234 809 012 3456',
                'date_of_birth' => '2004-04-25',
                'address' => '369 Willow Street, Jos, Nigeria',
                'status' => 'active',
            ],
            [
                'first_name' => 'Sophia',
                'last_name' => 'Taylor',
                'email' => 'sophia.taylor@student.globalcollege.edu',
                'phone' => '+234 810 123 4567',
                'date_of_birth' => '2005-10-12',
                'address' => '741 Ash Avenue, Calabar, Nigeria',
                'status' => 'active',
            ],
        ];

        foreach ($students as $student) {
            $student['student_number'] = 'STU-' . strtoupper(Str::random(8));
            Student::create($student);
        }
    }
}
