<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();
        $courses = Course::all();
        $cashier = User::where('email', 'cashier@globalcollege.edu')->first();
        $admin = User::where('email', 'admin@globalcollege.edu')->first();

        if ($students->isEmpty() || $courses->isEmpty() || !$cashier) {
            return;
        }

        // Create sample payments with varying discount scenarios
        $paymentScenarios = [
            // Full price payments (no discount)
            ['discount_percent' => 0, 'count' => 3],
            // Small discounts (5-10%)
            ['discount_percent' => 7, 'count' => 4],
            // Medium discounts (15-20%)
            ['discount_percent' => 18, 'count' => 2],
            // Large discounts (25-30%)
            ['discount_percent' => 28, 'count' => 1],
        ];

        $paymentMethods = ['cash', 'bank_transfer', 'card', 'check'];
        $notes = [
            null,
            'Early payment discount applied',
            'Scholarship student',
            'Family discount',
            'Bulk payment discount',
            'Promotional offer',
        ];

        $paymentCount = 0;
        foreach ($paymentScenarios as $scenario) {
            for ($i = 0; $i < $scenario['count']; $i++) {
                $student = $students->random();
                $course = $courses->random();
                $basePrice = $course->base_price;
                
                // Calculate amount paid based on discount percentage
                $discountAmount = ($basePrice * $scenario['discount_percent']) / 100;
                $amountPaid = $basePrice - $discountAmount;

                // Create payment
                $payment = Payment::create([
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'amount_paid' => round($amountPaid, 2),
                    'base_price' => $basePrice,
                    'discount_amount' => round($discountAmount, 2),
                    'cashier_id' => $cashier->id,
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'notes' => $notes[array_rand($notes)],
                    'created_at' => now()->subDays(rand(1, 30)), // Random date within last 30 days
                ]);

                // Create receipt for each payment
                Receipt::create([
                    'payment_id' => $payment->id,
                    'receipt_number' => 'RCP-' . strtoupper(Str::random(8)),
                    'receipt_date' => $payment->created_at->toDateString(),
                ]);

                $paymentCount++;
            }
        }

        // Create a few payments processed by admin
        for ($i = 0; $i < 2; $i++) {
            $student = $students->random();
            $course = $courses->random();
            $basePrice = $course->base_price;
            $discountAmount = ($basePrice * 15) / 100;
            $amountPaid = $basePrice - $discountAmount;

            $payment = Payment::create([
                'student_id' => $student->id,
                'course_id' => $course->id,
                'amount_paid' => round($amountPaid, 2),
                'base_price' => $basePrice,
                'discount_amount' => round($discountAmount, 2),
                'cashier_id' => $admin->id,
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'notes' => 'Admin processed payment',
                'created_at' => now()->subDays(rand(1, 15)),
            ]);

            Receipt::create([
                'payment_id' => $payment->id,
                'receipt_number' => 'RCP-' . strtoupper(Str::random(8)),
                'receipt_date' => $payment->created_at->toDateString(),
            ]);
        }
    }
}
