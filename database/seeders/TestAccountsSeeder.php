<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure roles exist
        $superAdminRole = Role::firstOrCreate(
            ['slug' => 'super-admin'],
            [
                'name' => 'Super Admin',
                'description' => 'Full system access with all permissions',
            ]
        );

        // Create Test Admin Account
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Test Admin',
                'password' => Hash::make('admin123'),
                'role_id' => $superAdminRole->id,
            ]
        );

        // Create Test Teacher Account
        $teacher = Teacher::where('employee_number', 'EMP-2025-00001')->first();
        
        if (!$teacher) {
            $teacher = Teacher::create([
                'employee_number' => 'EMP-2025-00001',
                'email' => 'teacher@test.com',
                'first_name' => 'Test',
                'last_name' => 'Teacher',
                'phone' => '254712345678',
                'date_of_birth' => '1985-01-15',
                'gender' => 'male',
                'qualification' => 'MSc Computer Science',
                'specialization' => 'Software Development',
                'hire_date' => '2024-01-01',
                'status' => 'active',
                'password' => 'EMP-2025-00001', // Default password (employee number)
            ]);
        } else {
            // Update password if teacher exists
            $teacher->update([
                'password' => 'EMP-2025-00001',
                'status' => 'active',
            ]);
        }

        // Assign teacher to a course if courses exist
        $course = Course::where('status', 'active')->first();
        if ($course) {
            $teacher->courses()->syncWithoutDetaching([$course->id]);
        }

        // Create Test Student Account
        $student = Student::where('student_number', 'STU-TEST001')
            ->orWhere('admission_number', 'ADM-2025-00001')
            ->first();
        
        if (!$student) {
            $student = Student::create([
                'email' => 'student@test.com',
                'student_number' => 'STU-TEST001',
                'admission_number' => 'ADM-2025-00001',
                'first_name' => 'Test',
                'last_name' => 'Student',
                'phone' => '254723456789',
                'date_of_birth' => '2000-05-20',
                'gender' => 'male',
                'level_of_education' => 'Diploma',
                'nationality' => 'Kenyan',
                'id_passport_number' => '12345678',
                'next_of_kin_name' => 'John Doe',
                'next_of_kin_mobile' => '254734567890',
                'status' => 'active',
                'password' => 'STU-TEST001', // Default password (student number)
            ]);
        } else {
            // Update password if student exists
            $student->update([
                'password' => 'STU-TEST001',
                'status' => 'active',
            ]);
        }

        $this->command->info('Test accounts created successfully!');
        $this->command->info('');
        $this->command->info('=== TEST CREDENTIALS ===');
        $this->command->info('');
        $this->command->info('ADMIN ACCOUNT:');
        $this->command->info('  Login URL: http://localhost:8000/login');
        $this->command->info('  Email: admin@test.com');
        $this->command->info('  Password: admin123');
        $this->command->info('');
        $this->command->info('TEACHER ACCOUNT:');
        $this->command->info('  Login URL: http://localhost:8000/teacher/login');
        $this->command->info('  Employee Number: EMP-2025-00001');
        $this->command->info('  Password: EMP-2025-00001');
        $this->command->info('');
        $this->command->info('STUDENT ACCOUNT:');
        $this->command->info('  Login URL: http://localhost:8000/student/login');
        $this->command->info('  Student Number: STU-TEST001');
        $this->command->info('  Password: STU-TEST001');
        $this->command->info('');
    }
}

