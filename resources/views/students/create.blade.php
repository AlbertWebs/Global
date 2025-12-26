@extends('layouts.app')

@section('title', 'Student Admission')
@section('page-title', 'Student Admission')

@section('content')
<div class="max-w-6xl mx-auto" x-data="admissionForm()">
    <!-- Progress Bar -->
    <div x-show="submitting" class="mb-6">
        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Submitting admission form...</span>
                <span class="text-sm font-medium text-gray-700" x-text="progress + '%'"></span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div 
                    class="bg-gradient-to-r from-blue-600 to-blue-700 h-2.5 rounded-full transition-all duration-300 ease-out"
                    :style="'width: ' + progress + '%'"
                ></div>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    <div x-show="success" x-cloak class="mb-6">
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-md">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1">
                    <p class="text-green-800 font-semibold" x-text="successMessage"></p>
                    <p class="text-green-700 text-sm mt-1">Redirecting to student profile...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Message -->
    <div x-show="error" x-cloak class="mb-6">
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-md">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1">
                    <p class="text-red-800 font-semibold" x-text="errorMessage"></p>
                </div>
            </div>
        </div>
    </div>

    <form @submit.prevent="submitForm" method="POST" action="{{ route('students.store') }}" id="admissionForm">
        @csrf
        
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
                <h2 class="text-2xl font-bold text-white">Student Admission Form</h2>
                <p class="text-blue-100 mt-1">Please fill in all required fields marked with *</p>
            </div>

            <div class="p-8">
                <!-- Personal Information Section -->
                <div class="mb-8">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Personal Information</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="admission_number" class="block text-sm font-semibold text-gray-700 mb-2">
                                Admission Number
                            </label>
                            <div class="relative">
                                <input 
                                    type="text" 
                                    id="admission_number" 
                                    name="admission_number" 
                                    value="Auto-generated"
                                    readonly
                                    disabled
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed"
                                >
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Will be automatically generated upon submission</p>
                        </div>

                        <div>
                            <label for="first_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="first_name" 
                                name="first_name" 
                                x-model="formData.first_name"
                                required
                                :class="errors.first_name ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500'"
                                class="w-full px-4 py-3 border-2 rounded-lg transition-colors"
                                placeholder="Enter first name"
                            >
                            <span x-show="errors.first_name" class="text-red-600 text-sm mt-1" x-text="errors.first_name"></span>
                        </div>

                        <div>
                            <label for="middle_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                Middle Name
                            </label>
                            <input 
                                type="text" 
                                id="middle_name" 
                                name="middle_name" 
                                x-model="formData.middle_name"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="Enter middle name (optional)"
                            >
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                Last Name <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="last_name" 
                                name="last_name" 
                                x-model="formData.last_name"
                                required
                                :class="errors.last_name ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500'"
                                class="w-full px-4 py-3 border-2 rounded-lg transition-colors"
                                placeholder="Enter last name"
                            >
                            <span x-show="errors.last_name" class="text-red-600 text-sm mt-1" x-text="errors.last_name"></span>
                        </div>

                        <div>
                            <label for="date_of_birth" class="block text-sm font-semibold text-gray-700 mb-2">
                                Date of Birth <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="date" 
                                id="date_of_birth" 
                                name="date_of_birth" 
                                x-model="formData.date_of_birth"
                                required
                                max="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            >
                            <span x-show="errors.date_of_birth" class="text-red-600 text-sm mt-1" x-text="errors.date_of_birth"></span>
                        </div>

                        <div>
                            <label for="gender" class="block text-sm font-semibold text-gray-700 mb-2">
                                Gender <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="gender" 
                                name="gender" 
                                x-model="formData.gender"
                                required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            >
                                <option value="">Select gender...</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                            <span x-show="errors.gender" class="text-red-600 text-sm mt-1" x-text="errors.gender"></span>
                        </div>
                    </div>
                </div>

                <!-- Contact Information Section -->
                <div class="mb-8 pt-8 border-t border-gray-200">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Contact Information</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                Contact Number <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="tel" 
                                id="phone" 
                                name="phone" 
                                x-model="formData.phone"
                                required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="+254 700 000 000"
                            >
                            <span x-show="errors.phone" class="text-red-600 text-sm mt-1" x-text="errors.phone"></span>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                Email Address
                            </label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                x-model="formData.email"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="student@example.com"
                            >
                            <span x-show="errors.email" class="text-red-600 text-sm mt-1" x-text="errors.email"></span>
                        </div>

                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">
                            Physical Address
                            </label>
                            <textarea 
                                id="address" 
                                name="address" 
                                x-model="formData.address"
                                rows="3"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="Enter full address (optional)"
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- Academic Information Section -->
                <div class="mb-8 pt-8 border-t border-gray-200">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Academic Information</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="level_of_education" class="block text-sm font-semibold text-gray-700 mb-2">
                                Level of Education <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="level_of_education" 
                                name="level_of_education" 
                                x-model="formData.level_of_education"
                                required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            >
                                <option value="">Select level...</option>
                                <option value="Primary">Primary</option>
                                <option value="High School">High School</option>
                                <option value="Diploma">Diploma</option>
                                <option value="Bachelor">Bachelor</option>
                                <option value="Master">Master</option>
                                <option value="PhD">PhD</option>
                            </select>
                            <span x-show="errors.level_of_education" class="text-red-600 text-sm mt-1" x-text="errors.level_of_education"></span>
                        </div>

                        <div>
                            <label for="nationality" class="block text-sm font-semibold text-gray-700 mb-2">
                                Nationality <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="nationality" 
                                name="nationality" 
                                x-model="formData.nationality"
                                required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="e.g., Kenyan"
                            >
                            <span x-show="errors.nationality" class="text-red-600 text-sm mt-1" x-text="errors.nationality"></span>
                        </div>

                        <div>
                            <label for="id_passport_number" class="block text-sm font-semibold text-gray-700 mb-2">
                                ID / Passport Number <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="id_passport_number" 
                                name="id_passport_number" 
                                x-model="formData.id_passport_number"
                                required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="Enter ID or Passport number"
                            >
                            <span x-show="errors.id_passport_number" class="text-red-600 text-sm mt-1" x-text="errors.id_passport_number"></span>
                        </div>
                    </div>
                </div>

                <!-- Guardian Section -->
                <div class="mb-8 pt-8 border-t border-gray-200">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Guardian</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="next_of_kin_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                Guardian Name
                            </label>
                            <input 
                                type="text" 
                                id="next_of_kin_name" 
                                name="next_of_kin_name" 
                                x-model="formData.next_of_kin_name"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="Enter full name (optional)"
                            >
                            <span x-show="errors.next_of_kin_name" class="text-red-600 text-sm mt-1" x-text="errors.next_of_kin_name"></span>
                        </div>

                        <div>
                            <label for="next_of_kin_mobile" class="block text-sm font-semibold text-gray-700 mb-2">
                                Guardian Mobile Number
                            </label>
                            <input 
                                type="tel" 
                                id="next_of_kin_mobile" 
                                name="next_of_kin_mobile" 
                                x-model="formData.next_of_kin_mobile"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="+254 700 000 000 (optional)"
                            >
                            <span x-show="errors.next_of_kin_mobile" class="text-red-600 text-sm mt-1" x-text="errors.next_of_kin_mobile"></span>
                        </div>
                    </div>
                </div>

                <!-- Status Section -->
                <div class="mb-8 pt-8 border-t border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="status" 
                                name="status" 
                                x-model="formData.status"
                                required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            >
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="graduated">Graduated</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t border-gray-200">
                    <a 
                        href="{{ route('students.index') }}" 
                        class="px-8 py-3 border-2 border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition-colors text-center"
                    >
                        Cancel
                    </a>
                    <button 
                        type="submit" 
                        :disabled="submitting"
                        class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
                    >
                        <span x-show="!submitting">Submit Admission</span>
                        <span x-show="submitting" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
[x-cloak] { display: none !important; }
</style>

<script>
function admissionForm() {
    return {
        formData: {
            first_name: '',
            middle_name: '',
            last_name: '',
            date_of_birth: '',
            phone: '',
            gender: '',
            email: '',
            level_of_education: '',
            nationality: '',
            id_passport_number: '',
            next_of_kin_name: '',
            next_of_kin_mobile: '',
            address: '',
            status: 'active'
        },
        errors: {},
        submitting: false,
        success: false,
        error: false,
        successMessage: '',
        errorMessage: '',
        progress: 0,
        progressInterval: null,

        async submitForm() {
            // Reset states
            this.errors = {};
            this.error = false;
            this.success = false;
            this.submitting = true;
            this.progress = 0;

            // Start progress animation
            this.progressInterval = setInterval(() => {
                if (this.progress < 90) {
                    this.progress += 10;
                }
            }, 200);

            try {
                const form = document.getElementById('admissionForm');
                const formData = new FormData(form);
                
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                // Complete progress
                clearInterval(this.progressInterval);
                this.progress = 100;

                if (response.ok && data.success) {
                    this.success = true;
                    const admissionNumber = data.student?.admission_number || 'N/A';
                    this.successMessage = `Student admitted successfully! Admission Number: ${admissionNumber}`;
                    
                    // Redirect after 3 seconds to show admission number
                    setTimeout(() => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            window.location.href = '{{ route("students.index") }}';
                        }
                    }, 3000);
                } else {
                    this.error = true;
                    this.errorMessage = data.message || 'An error occurred. Please check the form and try again.';
                    
                    // Handle validation errors
                    if (data.errors) {
                        // Convert Laravel validation errors format to simple object
                        const errorObj = {};
                        Object.keys(data.errors).forEach(key => {
                            errorObj[key] = Array.isArray(data.errors[key]) ? data.errors[key][0] : data.errors[key];
                        });
                        this.errors = errorObj;
                    }
                    
                    this.submitting = false;
                    this.progress = 0;
                    
                    // Scroll to first error
                    setTimeout(() => {
                        const firstError = document.querySelector('.text-red-600');
                        if (firstError) {
                            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    }, 100);
                }
            } catch (error) {
                clearInterval(this.progressInterval);
                this.progress = 0;
                this.submitting = false;
                this.error = true;
                this.errorMessage = 'Network error. Please check your connection and try again.';
                console.error('Error:', error);
            }
        }
    }
}
</script>
@endsection
