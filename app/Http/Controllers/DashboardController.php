<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $stats = [];

        if ($user->isSuperAdmin()) {
            $stats = [
                'total_students' => Student::count(),
                'total_courses' => Course::count(),
                'today_payments' => Payment::whereDate('created_at', today())->sum('amount_paid'),
                'month_payments' => Payment::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('amount_paid'),
                'total_discounts' => Payment::sum('discount_amount'),
            ];
        } else {
            $stats = [
                'total_students' => Student::count(),
                'total_courses' => Course::count(),
                'today_payments' => Payment::where('cashier_id', $user->id)
                    ->whereDate('created_at', today())
                    ->sum('amount_paid'),
            ];
        }

        return view('dashboard', compact('stats'));
    }
}
