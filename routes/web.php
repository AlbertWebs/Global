<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BankDepositController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseRegistrationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataPurgeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FeeBalanceController;
use App\Http\Controllers\MoneyTraceController;
use App\Http\Controllers\MobileDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Students
    Route::resource('students', StudentController::class);

    // Courses
    Route::resource('courses', CourseController::class);

    // Course Registrations
    Route::get('/course-registrations', [CourseRegistrationController::class, 'index'])->name('course-registrations.index');
    Route::get('/course-registrations/create', [CourseRegistrationController::class, 'create'])->name('course-registrations.create');
    Route::post('/course-registrations', [CourseRegistrationController::class, 'store'])->name('course-registrations.store');
    Route::delete('/course-registrations/{courseRegistration}', [CourseRegistrationController::class, 'destroy'])->name('course-registrations.destroy');

    // Billing
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
    Route::post('/billing', [BillingController::class, 'store'])->name('billing.store');
    Route::get('/billing/course/{courseId}', [BillingController::class, 'getCourseInfo'])->name('billing.course-info');
    Route::get('/billing/student/{studentId}/courses', [BillingController::class, 'getStudentCourses'])->name('billing.student-courses');

    // Receipts
    Route::get('/receipts', [ReceiptController::class, 'index'])->name('receipts.index');
    Route::get('/receipts/{id}', [ReceiptController::class, 'show'])->name('receipts.show');
    Route::get('/receipts/{id}/print', [ReceiptController::class, 'print'])->name('receipts.print');
    Route::get('/receipts/{id}/print-bw', [ReceiptController::class, 'printBw'])->name('receipts.print-bw');
    Route::get('/receipts/{id}/thermal', [ReceiptController::class, 'thermal'])->name('receipts.thermal');

    // Expenses (Cashier and Super Admin)
    Route::resource('expenses', ExpenseController::class);

    // Bank Deposits (Cashier and Super Admin)
    Route::resource('bank-deposits', BankDepositController::class);

    // Mobile Dashboard (Super Admin only)
    Route::get('/mobile', [MobileDashboardController::class, 'index'])->name('mobile.dashboard');

    // Reports (Super Admin only - checked in controller)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('/reports/export-expenses', [ReportController::class, 'exportExpenses'])->name('reports.export-expenses');

    // Fee Balances (Super Admin only - checked in controller)
    Route::get('/fee-balances', [FeeBalanceController::class, 'index'])->name('fee-balances.index');
    Route::post('/fee-balances/send-reminders', [FeeBalanceController::class, 'sendReminders'])->name('fee-balances.send-reminders');

    // Money Trace (Super Admin only - checked in controller)
    Route::get('/money-trace', [MoneyTraceController::class, 'index'])->name('money-trace.index');

    // Users & Roles (Super Admin only - checked in controller)
    Route::resource('users', UserController::class);

    // Role Permissions (Super Admin only - checked in controller)
    Route::get('/role-permissions', [RolePermissionController::class, 'index'])->name('role-permissions.index');
    Route::put('/role-permissions/{role}', [RolePermissionController::class, 'update'])->name('role-permissions.update');

    // Profile (All authenticated users)
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');

    // Settings (Super Admin only - checked in controller)
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Data Purge (Super Admin only - checked in controller)
    Route::get('/data-purge', [DataPurgeController::class, 'index'])->name('data-purge.index');
    Route::post('/data-purge', [DataPurgeController::class, 'purge'])->name('data-purge.purge');
});
