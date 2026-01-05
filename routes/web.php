<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\StudentLoginController;
use App\Http\Controllers\Auth\TeacherLoginController;
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
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\StudentPortalController;
use App\Http\Controllers\TeacherPortalController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Student Login Routes (Public)
Route::get('/student/login', [StudentLoginController::class, 'showLoginForm'])->name('student.login');
Route::post('/student/login', [StudentLoginController::class, 'login']);
Route::post('/student/logout', [StudentLoginController::class, 'logout'])->name('student.logout');

// Teacher Login Routes (Public)
Route::get('/teacher/login', [TeacherLoginController::class, 'showLoginForm'])->name('teacher.login');
Route::post('/teacher/login', [TeacherLoginController::class, 'login']);
Route::post('/teacher/logout', [TeacherLoginController::class, 'logout'])->name('teacher.logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Students
    Route::post('/students/{student}/send-welcome-sms', [StudentController::class, 'sendWelcomeSMS'])->name('students.send-welcome-sms');
    Route::resource('students', StudentController::class);

    // Courses
    Route::resource('courses', CourseController::class);

    // Course Registrations
    Route::get('/course-registrations', [CourseRegistrationController::class, 'index'])->name('course-registrations.index');
    Route::get('/course-registrations/create', [CourseRegistrationController::class, 'create'])->name('course-registrations.create');
    Route::post('/course-registrations', [CourseRegistrationController::class, 'store'])->name('course-registrations.store');
    Route::delete('/course-registrations/{courseRegistration}', [CourseRegistrationController::class, 'destroy'])->name('course-registrations.destroy');

    // API for course registrations
    Route::get('/api/students/{studentId}/registered-courses', [CourseRegistrationController::class, 'getRegisteredCoursesForStudent'])->name('api.students.registered-courses');

    // Billing
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
    Route::post('/billing', [BillingController::class, 'store'])->name('billing.store');
    Route::get('/billing/course/{courseId}', [BillingController::class, 'getCourseInfo'])->name('billing.course-info');
    Route::get('/billing/student/{studentId}/courses', [BillingController::class, 'getStudentCourses'])->name('billing.student-courses');
    Route::get('/billing/student/{studentId}/balance/{courseId}', [BillingController::class, 'getStudentBalance'])->name('billing.student-balance');
    Route::get('/billing/student/{studentId}/overall-balance', [BillingController::class, 'getStudentOverallBalance'])->name('billing.student-overall-balance');

    // Receipts
    Route::get('/receipts', [ReceiptController::class, 'index'])->name('receipts.index');
    Route::get('/receipts/{id}', [ReceiptController::class, 'show'])->name('receipts.show');
    Route::get('/receipts/{id}/print', [ReceiptController::class, 'print'])->name('receipts.print');
    Route::get('/receipts/{id}/print-bw', [ReceiptController::class, 'printBw'])->name('receipts.print-bw');
    Route::get('/receipts/{id}/thermal', [ReceiptController::class, 'thermal'])->name('receipts.thermal');

    // Expenses (Cashier and Super Admin)
    Route::resource('expenses', ExpenseController::class);

    // Bank Deposits (Cashier and Super Admin)
    Route::get('/bank-deposits/get-balance', [BankDepositController::class, 'getBalance'])->name('bank-deposits.get-balance');
    Route::resource('bank-deposits', BankDepositController::class);

    // Mobile Dashboard (Super Admin only)
    Route::get('/mobile', [MobileDashboardController::class, 'index'])->name('mobile.dashboard');

    // Reports (Super Admin only - checked in controller)
    Route::get('/reports', [ReportController::class, 'module'])->name('reports.index');
    Route::get('/reports/financial', [ReportController::class, 'index'])->name('reports.financial');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('/reports/export-payments', [ReportController::class, 'exportPayments'])->name('reports.export-payments');
    Route::get('/reports/export-expenses', [ReportController::class, 'exportExpenses'])->name('reports.export-expenses');
    Route::get('/reports/export-students-registered', [ReportController::class, 'exportStudentsRegistered'])->name('reports.export-students-registered');
    Route::get('/reports/export-balances', [ReportController::class, 'exportBalances'])->name('reports.export-balances');
    Route::get('/reports/export-course-registrations', [ReportController::class, 'exportCourseRegistrations'])->name('reports.export-course-registrations');
    Route::get('/reports/export-bank-deposits', [ReportController::class, 'exportBankDeposits'])->name('reports.export-bank-deposits');
    Route::get('/reports/export-receipts', [ReportController::class, 'exportReceipts'])->name('reports.export-receipts');

    // Fee Balances (Super Admin only - checked in controller)
    Route::get('/fee-balances', [FeeBalanceController::class, 'index'])->name('fee-balances.index');
    Route::post('/fee-balances/send-reminders', [FeeBalanceController::class, 'sendReminders'])->name('fee-balances.send-reminders');

    // Money Trace (Super Admin only - checked in controller)
    Route::get('/money-trace', [MoneyTraceController::class, 'index'])->name('money-trace.index');

    // Balances (Super Admin only - checked in controller)
    Route::resource('balances', \App\Http\Controllers\BalanceController::class)->except(['create', 'store', 'destroy']);

    // Users & Roles (Super Admin only - checked in controller)
    Route::resource('users', UserController::class);

    // Teachers Management (Super Admin only - checked in controller)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('teachers', TeacherController::class);
    });

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

    // Bulk SMS (Super Admin only - checked in controller)
    Route::get('/bulk-sms', [\App\Http\Controllers\BulkSmsController::class, 'index'])->name('bulk-sms.index');
    Route::post('/bulk-sms/send', [\App\Http\Controllers\BulkSmsController::class, 'send'])->name('bulk-sms.send');
    Route::get('/bulk-sms/students', [\App\Http\Controllers\BulkSmsController::class, 'getStudents'])->name('bulk-sms.students');
    Route::get('/bulk-sms/teachers', [\App\Http\Controllers\BulkSmsController::class, 'getTeachers'])->name('bulk-sms.teachers');

    // Data Purge (Super Admin only - checked in controller)
    Route::get('/data-purge', [DataPurgeController::class, 'index'])->name('data-purge.index');
    Route::post('/data-purge', [DataPurgeController::class, 'purge'])->name('data-purge.purge');

    // Student Portal (requires student authentication)
    Route::prefix('student-portal')->name('student-portal.')->middleware('student.auth')->group(function () {
        Route::get('/', [StudentPortalController::class, 'index'])->name('index');
        Route::get('/financial-info', [StudentPortalController::class, 'financialInfo'])->name('financial-info');
        Route::get('/courses', [StudentPortalController::class, 'courses'])->name('courses');
        Route::get('/announcements', [StudentPortalController::class, 'announcements'])->name('announcements');
        Route::get('/results', [StudentPortalController::class, 'results'])->name('results');
        Route::get('/settings', [StudentPortalController::class, 'settings'])->name('settings');
        Route::post('/change-password', [StudentPortalController::class, 'changePassword'])->name('change-password');
        Route::post('/upload-photo', [StudentPortalController::class, 'uploadPhoto'])->name('upload-photo');
        Route::post('/logout', [StudentLoginController::class, 'logout'])->name('logout');
        
        // Student-accessible receipt routes
        Route::get('/receipts/{id}', [ReceiptController::class, 'studentShow'])->name('receipts.show');
        Route::get('/receipts/{id}/print', [ReceiptController::class, 'studentPrint'])->name('receipts.print');
        Route::get('/receipts/{id}/print-bw', [ReceiptController::class, 'studentPrintBw'])->name('receipts.print-bw');
        Route::get('/receipts/{id}/thermal', [ReceiptController::class, 'studentThermal'])->name('receipts.thermal');
    });

    // Teacher Portal (requires teacher authentication)
    Route::prefix('teacher-portal')->name('teacher-portal.')->middleware('teacher.auth')->group(function () {
        Route::get('/', [TeacherPortalController::class, 'index'])->name('index');
        Route::get('/personal-info', [TeacherPortalController::class, 'personalInfo'])->name('personal-info');
        Route::get('/courses', [TeacherPortalController::class, 'courses'])->name('courses');
        Route::get('/student-progress', [TeacherPortalController::class, 'studentProgress'])->name('student-progress');
        Route::get('/post-results', [TeacherPortalController::class, 'postResults'])->name('post-results');
        Route::post('/post-results', [TeacherPortalController::class, 'storeResult'])->name('store-result');
        Route::get('/results/{id}/edit', [TeacherPortalController::class, 'editResult'])->name('edit-result');
        Route::put('/results/{id}', [TeacherPortalController::class, 'updateResult'])->name('update-result');
        Route::get('/communicate', [TeacherPortalController::class, 'communicate'])->name('communicate');
        Route::post('/communicate', [TeacherPortalController::class, 'storeAnnouncement'])->name('store-announcement');
        Route::get('/announcements/{id}/edit', [TeacherPortalController::class, 'editAnnouncement'])->name('edit-announcement');
        Route::put('/announcements/{id}', [TeacherPortalController::class, 'updateAnnouncement'])->name('update-announcement');
        Route::delete('/announcements/{id}', [TeacherPortalController::class, 'deleteAnnouncement'])->name('delete-announcement');
        Route::get('/attendance', [TeacherPortalController::class, 'attendance'])->name('attendance');
        Route::post('/attendance', [TeacherPortalController::class, 'markAttendance'])->name('mark-attendance');
        Route::get('/courses/{courseId}/students', [TeacherPortalController::class, 'getCourseStudents'])->name('course-students');
        Route::get('/settings', [TeacherPortalController::class, 'settings'])->name('settings');
        Route::post('/change-password', [TeacherPortalController::class, 'changePassword'])->name('change-password');
        Route::post('/upload-photo', [TeacherPortalController::class, 'uploadPhoto'])->name('upload-photo');
        Route::put('/personal-info', [TeacherPortalController::class, 'updatePersonalInfo'])->name('update-personal-info');
        Route::post('/logout', [TeacherLoginController::class, 'logout'])->name('logout');
    });
});
