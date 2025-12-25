<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BulkSmsController extends Controller
{
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Check if user is Super Admin
     */
    private function checkSuperAdmin()
    {
        if (!auth()->user() || !auth()->user()->isSuperAdmin()) {
            abort(403, 'Only Super Administrators can send bulk SMS.');
        }
    }

    public function index()
    {
        $this->checkSuperAdmin();
        
        $studentCount = Student::where('status', 'active')->count();
        $teacherCount = Teacher::where('status', 'active')->count();
        
        return view('bulk-sms.index', compact('studentCount', 'teacherCount'));
    }

    public function send(Request $request)
    {
        $this->checkSuperAdmin();
        
        Log::info('Bulk SMS request received', [
            'user_id' => auth()->id(),
            'recipient_type' => $request->input('recipient_type'),
            'message_length' => strlen($request->input('message', '')),
            'student_id' => $request->input('student_id'),
            'teacher_id' => $request->input('teacher_id'),
            'phone_number' => $request->input('phone_number'),
            'all_inputs' => $request->all(),
        ]);

        try {
            // Build validation rules based on recipient type
            $rules = [
                'recipient_type' => 'required|in:all_students,all_teachers,individual_student,individual_teacher,custom_number',
                'message' => 'required|string|max:1000',
            ];
            
            // Only validate student_id if recipient_type is individual_student
            if ($request->input('recipient_type') === 'individual_student') {
                $rules['student_id'] = 'required|exists:students,id';
            } else {
                $rules['student_id'] = 'nullable';
            }
            
            // Only validate teacher_id if recipient_type is individual_teacher
            if ($request->input('recipient_type') === 'individual_teacher') {
                $rules['teacher_id'] = 'required|exists:teachers,id';
            } else {
                $rules['teacher_id'] = 'nullable';
            }
            
            // Only validate phone_number if recipient_type is custom_number
            if ($request->input('recipient_type') === 'custom_number') {
                $rules['phone_number'] = 'required|string|max:20';
            } else {
                $rules['phone_number'] = 'nullable|string|max:20';
            }
            
            $validated = $request->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Bulk SMS validation failed', [
                'user_id' => auth()->id(),
                'errors' => $e->errors(),
            ]);
            return redirect()->route('bulk-sms.index')
                ->withErrors($e->errors())
                ->withInput();
        }

        $message = $validated['message'];
        $results = [
            'total' => 0,
            'success' => 0,
            'failed' => 0,
            'details' => [],
        ];

        Log::info('Bulk SMS processing started', [
            'user_id' => auth()->id(),
            'recipient_type' => $validated['recipient_type'],
            'message_preview' => substr($message, 0, 50) . '...',
        ]);

        try {
            switch ($validated['recipient_type']) {
                case 'all_students':
                    $students = Student::where('status', 'active')
                        ->whereNotNull('phone')
                        ->get();
                    
                    $results['total'] = $students->count();
                    
                    foreach ($students as $student) {
                        try {
                            Log::info('Sending SMS to student', [
                                'student_id' => $student->id,
                                'student_name' => $student->full_name,
                                'phone' => $student->phone,
                            ]);
                            
                            $success = $this->smsService->sendSMSs($message, $student);
                            
                            if ($success) {
                                $results['success']++;
                                $results['details'][] = [
                                    'name' => $student->full_name,
                                    'phone' => $student->phone,
                                    'status' => 'success',
                                ];
                                Log::info('SMS sent successfully to student', [
                                    'student_id' => $student->id,
                                    'phone' => $student->phone,
                                ]);
                            } else {
                                $results['failed']++;
                                $results['details'][] = [
                                    'name' => $student->full_name,
                                    'phone' => $student->phone,
                                    'status' => 'failed',
                                    'error' => 'SMS service returned false',
                                ];
                                Log::warning('SMS failed to send to student', [
                                    'student_id' => $student->id,
                                    'phone' => $student->phone,
                                ]);
                            }
                        } catch (\Exception $e) {
                            $results['failed']++;
                            $errorMsg = $e->getMessage();
                            $results['details'][] = [
                                'name' => $student->full_name,
                                'phone' => $student->phone,
                                'status' => 'failed',
                                'error' => $errorMsg,
                            ];
                            Log::error('Exception sending SMS to student', [
                                'student_id' => $student->id,
                                'phone' => $student->phone,
                                'error' => $errorMsg,
                                'trace' => $e->getTraceAsString(),
                            ]);
                        }
                    }
                    break;

                case 'all_teachers':
                    $teachers = Teacher::where('status', 'active')
                        ->whereNotNull('phone')
                        ->get();
                    
                    $results['total'] = $teachers->count();
                    
                    foreach ($teachers as $teacher) {
                        try {
                            Log::info('Sending SMS to teacher', [
                                'teacher_id' => $teacher->id,
                                'teacher_name' => $teacher->full_name,
                                'phone' => $teacher->phone,
                            ]);
                            
                            $success = $this->smsService->sendTeacherSMS($message, $teacher);
                            
                            if ($success) {
                                $results['success']++;
                                $results['details'][] = [
                                    'name' => $teacher->full_name,
                                    'phone' => $teacher->phone,
                                    'status' => 'success',
                                ];
                                Log::info('SMS sent successfully to teacher', [
                                    'teacher_id' => $teacher->id,
                                    'phone' => $teacher->phone,
                                ]);
                            } else {
                                $results['failed']++;
                                $results['details'][] = [
                                    'name' => $teacher->full_name,
                                    'phone' => $teacher->phone,
                                    'status' => 'failed',
                                    'error' => 'SMS service returned false',
                                ];
                                Log::warning('SMS failed to send to teacher', [
                                    'teacher_id' => $teacher->id,
                                    'phone' => $teacher->phone,
                                ]);
                            }
                        } catch (\Exception $e) {
                            $results['failed']++;
                            $errorMsg = $e->getMessage();
                            $results['details'][] = [
                                'name' => $teacher->full_name,
                                'phone' => $teacher->phone,
                                'status' => 'failed',
                                'error' => $errorMsg,
                            ];
                            Log::error('Exception sending SMS to teacher', [
                                'teacher_id' => $teacher->id,
                                'phone' => $teacher->phone,
                                'error' => $errorMsg,
                                'trace' => $e->getTraceAsString(),
                            ]);
                        }
                    }
                    break;

                case 'individual_student':
                    $student = Student::findOrFail($validated['student_id']);
                    $results['total'] = 1;
                    
                    Log::info('Sending SMS to individual student', [
                        'student_id' => $student->id,
                        'student_name' => $student->full_name,
                        'phone' => $student->phone,
                    ]);
                    
                    try {
                        $success = $this->smsService->sendSMSs($message, $student);
                        if ($success) {
                            $results['success'] = 1;
                            $results['details'][] = [
                                'name' => $student->full_name,
                                'phone' => $student->phone,
                                'status' => 'success',
                            ];
                            Log::info('SMS sent successfully to individual student', [
                                'student_id' => $student->id,
                                'phone' => $student->phone,
                            ]);
                        } else {
                            $results['failed'] = 1;
                            $results['details'][] = [
                                'name' => $student->full_name,
                                'phone' => $student->phone,
                                'status' => 'failed',
                                'error' => 'SMS service returned false',
                            ];
                            Log::warning('SMS failed to send to individual student', [
                                'student_id' => $student->id,
                                'phone' => $student->phone,
                            ]);
                        }
                    } catch (\Exception $e) {
                        $errorMsg = $e->getMessage();
                        $results['failed'] = 1;
                        $results['details'][] = [
                            'name' => $student->full_name,
                            'phone' => $student->phone,
                            'status' => 'failed',
                            'error' => $errorMsg,
                        ];
                        Log::error('Exception sending SMS to individual student', [
                            'student_id' => $student->id,
                            'phone' => $student->phone,
                            'error' => $errorMsg,
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }
                    break;

                case 'individual_teacher':
                    $teacher = Teacher::findOrFail($validated['teacher_id']);
                    $results['total'] = 1;
                    
                    Log::info('Sending SMS to individual teacher', [
                        'teacher_id' => $teacher->id,
                        'teacher_name' => $teacher->full_name,
                        'phone' => $teacher->phone,
                    ]);
                    
                    try {
                        $success = $this->smsService->sendTeacherSMS($message, $teacher);
                        if ($success) {
                            $results['success'] = 1;
                            $results['details'][] = [
                                'name' => $teacher->full_name,
                                'phone' => $teacher->phone,
                                'status' => 'success',
                            ];
                            Log::info('SMS sent successfully to individual teacher', [
                                'teacher_id' => $teacher->id,
                                'phone' => $teacher->phone,
                            ]);
                        } else {
                            $results['failed'] = 1;
                            $results['details'][] = [
                                'name' => $teacher->full_name,
                                'phone' => $teacher->phone,
                                'status' => 'failed',
                                'error' => 'SMS service returned false',
                            ];
                            Log::warning('SMS failed to send to individual teacher', [
                                'teacher_id' => $teacher->id,
                                'phone' => $teacher->phone,
                            ]);
                        }
                    } catch (\Exception $e) {
                        $errorMsg = $e->getMessage();
                        $results['failed'] = 1;
                        $results['details'][] = [
                            'name' => $teacher->full_name,
                            'phone' => $teacher->phone,
                            'status' => 'failed',
                            'error' => $errorMsg,
                        ];
                        Log::error('Exception sending SMS to individual teacher', [
                            'teacher_id' => $teacher->id,
                            'phone' => $teacher->phone,
                            'error' => $errorMsg,
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }
                    break;

                case 'custom_number':
                    $phoneNumber = $validated['phone_number'];
                    $results['total'] = 1;
                    
                    Log::info('Sending SMS to custom number', [
                        'phone' => $phoneNumber,
                    ]);
                    
                    try {
                        $success = $this->smsService->sendToPhoneNumber($phoneNumber, $message);
                        if ($success) {
                            $results['success'] = 1;
                            $results['details'][] = [
                                'name' => 'Custom Recipient',
                                'phone' => $phoneNumber,
                                'status' => 'success',
                            ];
                            Log::info('SMS sent successfully to custom number', [
                                'phone' => $phoneNumber,
                            ]);
                        } else {
                            $results['failed'] = 1;
                            $results['details'][] = [
                                'name' => 'Custom Recipient',
                                'phone' => $phoneNumber,
                                'status' => 'failed',
                                'error' => 'SMS service returned false',
                            ];
                            Log::warning('SMS failed to send to custom number', [
                                'phone' => $phoneNumber,
                            ]);
                        }
                    } catch (\Exception $e) {
                        $errorMsg = $e->getMessage();
                        $results['failed'] = 1;
                        $results['details'][] = [
                            'name' => 'Custom Recipient',
                            'phone' => $phoneNumber,
                            'status' => 'failed',
                            'error' => $errorMsg,
                        ];
                        Log::error('Exception sending SMS to custom number', [
                            'phone' => $phoneNumber,
                            'error' => $errorMsg,
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }
                    break;
            }

            Log::info('Bulk SMS processing completed', [
                'user_id' => auth()->id(),
                'recipient_type' => $validated['recipient_type'],
                'total' => $results['total'],
                'success' => $results['success'],
                'failed' => $results['failed'],
                'results' => $results,
            ]);

            // Create a more descriptive success message
            if ($results['total'] == 1) {
                // Single recipient
                if ($results['success'] == 1) {
                    $successMessage = "SMS sent successfully to {$results['details'][0]['name']} ({$results['details'][0]['phone']})!";
                } else {
                    $errorMessage = "Failed to send SMS to {$results['details'][0]['name']} ({$results['details'][0]['phone']}).";
                    if (isset($results['details'][0]['error'])) {
                        $errorMessage .= " Error: " . $results['details'][0]['error'];
                    }
                }
            } else {
                // Multiple recipients
                if ($results['success'] > 0 && $results['failed'] == 0) {
                    $successMessage = "Bulk SMS completed successfully! All {$results['success']} message(s) sent successfully.";
                } elseif ($results['success'] > 0 && $results['failed'] > 0) {
                    $successMessage = "Bulk SMS partially completed. {$results['success']} message(s) sent successfully, {$results['failed']} failed out of {$results['total']} total recipients.";
                } else {
                    $errorMessage = "Bulk SMS failed! All {$results['failed']} message(s) failed to send out of {$results['total']} total recipients.";
                }
            }

            // Always include results in the redirect
            Log::info('Redirecting with results', [
                'has_success_message' => isset($successMessage),
                'has_error_message' => isset($errorMessage),
                'results_summary' => [
                    'total' => $results['total'],
                    'success' => $results['success'],
                    'failed' => $results['failed'],
                ],
            ]);
            
            $redirect = redirect()->route('bulk-sms.index')->with('results', $results);
            
            if (isset($successMessage)) {
                return $redirect->with('success', $successMessage);
            } else {
                return $redirect->with('error', $errorMessage ?? 'Failed to send SMS.');
            }

        } catch (\Exception $e) {
            Log::error('Bulk SMS failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('bulk-sms.index')
                ->with('error', 'Failed to send bulk SMS: ' . $e->getMessage());
        }
    }

    public function getStudents(Request $request)
    {
        $this->checkSuperAdmin();
        $query = $request->get('q', '');
        
        $students = Student::where('status', 'active')
            ->whereNotNull('phone')
            ->where(function($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('student_number', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%");
            })
            ->limit(20)
            ->get()
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'text' => $student->full_name . ' (' . $student->student_number . ') - ' . $student->phone,
                ];
            });

        return response()->json($students);
    }

    public function getTeachers(Request $request)
    {
        $this->checkSuperAdmin();
        $query = $request->get('q', '');
        
        $teachers = Teacher::where('status', 'active')
            ->whereNotNull('phone')
            ->where(function($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('employee_number', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%");
            })
            ->limit(20)
            ->get()
            ->map(function($teacher) {
                return [
                    'id' => $teacher->id,
                    'text' => $teacher->full_name . ' (' . $teacher->employee_number . ') - ' . $teacher->phone,
                ];
            });

        return response()->json($teachers);
    }
}

