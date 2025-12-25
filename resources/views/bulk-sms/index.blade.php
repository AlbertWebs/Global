@extends('layouts.app')

@section('title', 'Bulk SMS')

@section('content')
<div class="p-6">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Bulk SMS</h1>
            <p class="text-gray-600">Send SMS messages to students, teachers, or individuals</p>
        </div>

        @if(session('success'))
            <div id="successAlert" class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-800 px-6 py-4 rounded-lg shadow-md flex items-start relative">
                <svg class="w-6 h-6 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1">
                    <h3 class="font-bold text-lg mb-1">Success!</h3>
                    <p>{{ session('success') }}</p>
                </div>
                <button onclick="document.getElementById('successAlert').remove()" class="ml-4 text-green-600 hover:text-green-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div id="errorAlert" class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-lg shadow-md flex items-start relative animate-fade-in">
                <svg class="w-6 h-6 text-red-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1">
                    <h3 class="font-bold text-lg mb-1">Error!</h3>
                    <p>{{ session('error') }}</p>
                </div>
                <button onclick="document.getElementById('errorAlert').remove()" class="ml-4 text-red-600 hover:text-red-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div id="validationErrors" class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-lg shadow-md animate-fade-in">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-red-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1">
                        <h3 class="font-bold text-lg mb-2">Validation Errors</h3>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button onclick="document.getElementById('validationErrors').remove()" class="ml-4 text-red-600 hover:text-red-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        @if(session('results'))
            @php $results = session('results'); @endphp
            <div id="resultsSection" class="mb-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 rounded-lg p-6 shadow-md animate-fade-in">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="font-bold text-lg text-blue-900">SMS Sending Results</h3>
                    </div>
                    <button onclick="document.getElementById('resultsSection').remove()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div class="text-center bg-white rounded-lg p-4 shadow-sm">
                        <div class="text-3xl font-bold text-blue-600">{{ $results['total'] }}</div>
                        <div class="text-sm text-gray-600 font-medium">Total Recipients</div>
                    </div>
                    <div class="text-center bg-white rounded-lg p-4 shadow-sm">
                        <div class="text-3xl font-bold text-green-600">{{ $results['success'] }}</div>
                        <div class="text-sm text-gray-600 font-medium">Successfully Sent</div>
                    </div>
                    <div class="text-center bg-white rounded-lg p-4 shadow-sm">
                        <div class="text-3xl font-bold text-red-600">{{ $results['failed'] }}</div>
                        <div class="text-sm text-gray-600 font-medium">Failed</div>
                    </div>
                </div>
                
                @if($results['success'] > 0 && $results['failed'] == 0)
                    <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg mb-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-semibold">All messages sent successfully!</span>
                        </div>
                    </div>
                @elseif($results['failed'] > 0 && $results['success'] == 0)
                    <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg mb-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-semibold">All messages failed to send. Please check the details below.</span>
                        </div>
                    </div>
                @elseif($results['failed'] > 0)
                    <div class="bg-yellow-100 border border-yellow-300 text-yellow-800 px-4 py-3 rounded-lg mb-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <span class="font-semibold">Some messages failed to send. Check the details below.</span>
                        </div>
                    </div>
                @endif
                @if(count($results['details']) > 0)
                    @if(count($results['details']) <= 20)
                        <div class="max-h-60 overflow-y-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-blue-100">
                                    <tr>
                                        <th class="px-3 py-2 text-left">Name</th>
                                        <th class="px-3 py-2 text-left">Phone</th>
                                        <th class="px-3 py-2 text-left">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results['details'] as $detail)
                                    <tr class="border-b hover:bg-blue-50">
                                        <td class="px-3 py-2">{{ $detail['name'] }}</td>
                                        <td class="px-3 py-2">{{ $detail['phone'] }}</td>
                                        <td class="px-3 py-2">
                                            @if($detail['status'] === 'success')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Sent
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                    Failed
                                                </span>
                                                @if(isset($detail['error']))
                                                    <div class="text-xs text-gray-500 mt-1">{{ $detail['error'] }}</div>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <p class="text-sm text-gray-600">
                                <strong>{{ count($results['details']) }}</strong> recipients processed. Details are only shown for up to 20 recipients. 
                                Summary: <span class="text-green-600 font-semibold">{{ $results['success'] }} sent</span>, 
                                <span class="text-red-600 font-semibold">{{ $results['failed'] }} failed</span>.
                            </p>
                        </div>
                    @endif
                @endif
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-lg p-6">
            <form method="POST" action="{{ route('bulk-sms.send') }}" id="bulkSmsForm" x-data="bulkSms()" onsubmit="return window.bulkSmsFormSubmit(event)">
                @csrf

                <!-- Recipient Type -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Select Recipients</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition-all" :class="recipientType === 'all_students' ? 'border-blue-500 bg-blue-50' : ''">
                            <input type="radio" name="recipient_type" value="all_students" x-model="recipientType" class="mr-3" required>
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900">All Students</div>
                                <div class="text-sm text-gray-600">{{ $studentCount }} active students</div>
                            </div>
                        </label>

                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition-all" :class="recipientType === 'all_teachers' ? 'border-blue-500 bg-blue-50' : ''">
                            <input type="radio" name="recipient_type" value="all_teachers" x-model="recipientType" class="mr-3" required>
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900">All Teachers</div>
                                <div class="text-sm text-gray-600">{{ $teacherCount }} active teachers</div>
                            </div>
                        </label>

                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition-all" :class="recipientType === 'individual_student' ? 'border-blue-500 bg-blue-50' : ''">
                            <input type="radio" name="recipient_type" value="individual_student" x-model="recipientType" class="mr-3" required>
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900">Individual Student</div>
                                <div class="text-sm text-gray-600">Select a specific student</div>
                            </div>
                        </label>

                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition-all" :class="recipientType === 'individual_teacher' ? 'border-blue-500 bg-blue-50' : ''">
                            <input type="radio" name="recipient_type" value="individual_teacher" x-model="recipientType" class="mr-3" required>
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900">Individual Teacher</div>
                                <div class="text-sm text-gray-600">Select a specific teacher</div>
                            </div>
                        </label>

                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition-all" :class="recipientType === 'custom_number' ? 'border-blue-500 bg-blue-50' : ''">
                            <input type="radio" name="recipient_type" value="custom_number" x-model="recipientType" class="mr-3" required>
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900">Custom Number</div>
                                <div class="text-sm text-gray-600">Enter a phone number manually</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Custom Phone Number Input -->
                <div x-show="recipientType === 'custom_number'" x-transition class="mb-6" style="display: none;">
                    <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input 
                        type="text" 
                        name="phone_number" 
                        id="phone_number"
                        placeholder="e.g., 254712345678 or +254712345678"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                    <p class="mt-2 text-xs text-gray-500">Enter the phone number with country code (e.g., 254712345678 for Kenya)</p>
                </div>

                <!-- Individual Student Selection -->
                <div x-show="recipientType === 'individual_student'" x-transition class="mb-6" style="display: none;">
                    <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">Select Student</label>
                    <select 
                        name="student_id" 
                        id="student_id"
                        :required="recipientType === 'individual_student'"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">Search and select a student...</option>
                    </select>
                </div>

                <!-- Individual Teacher Selection -->
                <div x-show="recipientType === 'individual_teacher'" x-transition class="mb-6" style="display: none;">
                    <label for="teacher_id" class="block text-sm font-medium text-gray-700 mb-2">Select Teacher</label>
                    <select 
                        name="teacher_id" 
                        id="teacher_id"
                        :required="recipientType === 'individual_teacher'"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">Search and select a teacher...</option>
                    </select>
                </div>

                <!-- Message -->
                <div class="mb-6">
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                        Message <span class="text-gray-500">(<span x-text="messageLength"></span> / 1000 characters)</span>
                    </label>
                    <textarea 
                        name="message" 
                        id="message"
                        rows="6"
                        maxlength="1000"
                        x-model="message"
                        @input="updateMessageLength"
                        required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Type your message here..."
                    ></textarea>
                    <p class="mt-2 text-xs text-gray-500">
                        <span x-show="messageLength > 160" class="text-orange-600 font-semibold">
                            Long message! This will be split into multiple SMS (approximately <span x-text="Math.ceil(messageLength / 160)"></span> messages).
                        </span>
                        <span x-show="messageLength <= 160" class="text-green-600">
                            Single SMS message (approximately 1 SMS).
                        </span>
                    </p>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4">
                    <button 
                        type="button"
                        onclick="window.location.reload()"
                        class="px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition-colors"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit"
                        :disabled="sending"
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg font-bold hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span x-show="!sending">Send SMS</span>
                        <span x-show="sending" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Sending...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function bulkSms() {
    return {
        recipientType: '',
        message: '',
        messageLength: 0,
        sending: false,
        
        init() {
            this.updateMessageLength();
            
            // Watch for recipient type changes to initialize Select2
            this.$watch('recipientType', (value) => {
                this.$nextTick(() => {
                    if (value === 'individual_student') {
                        setTimeout(() => {
                            $('#student_id').select2({
                                placeholder: 'Search and select a student...',
                                allowClear: true,
                                ajax: {
                                    url: '{{ route("bulk-sms.students") }}',
                                    dataType: 'json',
                                    delay: 250,
                                    data: function (params) {
                                        return {
                                            q: params.term,
                                            page: params.page
                                        };
                                    },
                                    processResults: function (data) {
                                        return {
                                            results: data
                                        };
                                    },
                                    cache: true
                                },
                                minimumInputLength: 1
                            });
                        }, 100);
                    } else {
                        $('#student_id').select2('destroy');
                    }
                    
                    if (value === 'individual_teacher') {
                        setTimeout(() => {
                            $('#teacher_id').select2({
                                placeholder: 'Search and select a teacher...',
                                allowClear: true,
                                ajax: {
                                    url: '{{ route("bulk-sms.teachers") }}',
                                    dataType: 'json',
                                    delay: 250,
                                    data: function (params) {
                                        return {
                                            q: params.term,
                                            page: params.page
                                        };
                                    },
                                    processResults: function (data) {
                                        return {
                                            results: data
                                        };
                                    },
                                    cache: true
                                },
                                minimumInputLength: 1
                            });
                        }, 100);
                    } else {
                        $('#teacher_id').select2('destroy');
                    }
                });
            });
        },
        
        updateMessageLength() {
            this.messageLength = this.message.length;
        },
        
        handleSubmit(event) {
            console.log('Form submission started', {
                recipientType: this.recipientType,
                messageLength: this.message.length
            });
            
            // Validate form before submission
            const form = event.target;
            
            // Check if recipient type is selected
            if (!this.recipientType) {
                event.preventDefault();
                alert('Please select a recipient type');
                return false;
            }
            
            // Check if message is provided
            if (!this.message || this.message.trim().length === 0) {
                event.preventDefault();
                alert('Please enter a message');
                return false;
            }
            
            // Check conditional fields based on recipient type
            if (this.recipientType === 'individual_student') {
                const studentSelect = document.getElementById('student_id');
                if (!studentSelect || !studentSelect.value) {
                    event.preventDefault();
                    alert('Please select a student');
                    return false;
                }
            }
            
            if (this.recipientType === 'individual_teacher') {
                const teacherSelect = document.getElementById('teacher_id');
                if (!teacherSelect || !teacherSelect.value) {
                    event.preventDefault();
                    alert('Please select a teacher');
                    return false;
                }
            }
            
            if (this.recipientType === 'custom_number') {
                const phoneInput = document.getElementById('phone_number');
                if (!phoneInput || !phoneInput.value || phoneInput.value.trim().length === 0) {
                    event.preventDefault();
                    alert('Please enter a phone number');
                    return false;
                }
            }
            
            // Set sending state
            this.sending = true;
            console.log('Form validation passed, submitting...');
            
            // Disable submit button to prevent double submission
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
            }
            
            // Allow form to submit normally - don't prevent default
            // The form will submit via normal POST request
        }
    }
}

// Form submission handler
window.bulkSmsFormSubmit = function(event) {
    const form = event.target;
    const formData = new FormData(form);
    
    console.log('Form submission started', {
        recipientType: formData.get('recipient_type'),
        messageLength: formData.get('message')?.length || 0
    });
    
    // Get Alpine.js component instance if available
    const alpineComponent = Alpine.$data(form);
    
    // Validate form
    const recipientType = formData.get('recipient_type');
    const message = formData.get('message');
    
    if (!recipientType) {
        event.preventDefault();
        alert('Please select a recipient type');
        return false;
    }
    
    if (!message || message.trim().length === 0) {
        event.preventDefault();
        alert('Please enter a message');
        return false;
    }
    
    // Remove unused fields from form data to prevent validation errors
    if (recipientType !== 'individual_student') {
        const studentSelect = form.querySelector('#student_id');
        if (studentSelect) {
            studentSelect.removeAttribute('name');
        }
    }
    
    if (recipientType !== 'individual_teacher') {
        const teacherSelect = form.querySelector('#teacher_id');
        if (teacherSelect) {
            teacherSelect.removeAttribute('name');
        }
    }
    
    if (recipientType !== 'custom_number') {
        const phoneInput = form.querySelector('#phone_number');
        if (phoneInput) {
            phoneInput.removeAttribute('name');
        }
    }
    
    // Check conditional fields
    if (recipientType === 'individual_student') {
        const studentId = formData.get('student_id');
        if (!studentId) {
            event.preventDefault();
            alert('Please select a student');
            return false;
        }
    }
    
    if (recipientType === 'individual_teacher') {
        const teacherId = formData.get('teacher_id');
        if (!teacherId) {
            event.preventDefault();
            alert('Please select a teacher');
            return false;
        }
    }
    
    if (recipientType === 'custom_number') {
        const phoneNumber = formData.get('phone_number');
        if (!phoneNumber || phoneNumber.trim().length === 0) {
            event.preventDefault();
            alert('Please enter a phone number');
            return false;
        }
    }
    
    // Set sending state if Alpine component is available
    if (alpineComponent) {
        alpineComponent.sending = true;
    }
    
    // Disable submit button
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
    }
    
    console.log('Form validation passed, submitting normally...');
    // Allow form to submit normally
    return true;
};

// Auto-scroll to results when page loads with results
document.addEventListener('DOMContentLoaded', function() {
    @if(session('results'))
        const resultsSection = document.getElementById('resultsSection');
        if (resultsSection) {
            setTimeout(() => {
                resultsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
        }
    @endif
    
    // Log if results are available
    @if(session('results'))
        console.log('SMS Results available:', @json(session('results')));
    @endif
    
    @if(session('success'))
        console.log('Success message:', @json(session('success')));
    @endif
    
    @if(session('error'))
        console.log('Error message:', @json(session('error')));
    @endif
});
</script>

@endsection

@push('head')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush

