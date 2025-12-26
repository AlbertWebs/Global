@extends('layouts.app')

@section('title', 'Edit Student')
@section('page-title', 'Edit Student Information')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
            <h2 class="text-2xl font-bold text-white">Edit Student Information</h2>
            <p class="text-blue-100 mt-1">Update student details below</p>
        </div>

        <form method="POST" action="{{ route('students.update', $student->id) }}" enctype="multipart/form-data" class="p-8">
            @csrf
            @method('PUT')

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
                    <!-- Student Photo -->
                    <div class="md:col-span-2">
                        <label for="photo" class="block text-sm font-semibold text-gray-700 mb-2">
                            Student Photo
                        </label>
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                @if($student->photo)
                                    <div id="photo-preview" class="w-24 h-24 border-2 border-gray-300 rounded-lg overflow-hidden">
                                        <img id="photo-preview-img" src="{{ asset('storage/' . $student->photo) }}" alt="Student Photo" class="w-full h-full object-cover">
                                    </div>
                                    <div id="photo-placeholder" class="hidden"></div>
                                @else
                                    <div id="photo-preview" class="w-24 h-24 bg-gray-100 border-2 border-gray-300 rounded-lg flex items-center justify-center overflow-hidden hidden">
                                        <img id="photo-preview-img" src="" alt="Photo Preview" class="w-full h-full object-cover">
                                    </div>
                                    <div id="photo-placeholder" class="w-24 h-24 bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <input 
                                    type="file" 
                                    id="photo" 
                                    name="photo" 
                                    accept="image/jpeg,image/jpg,image/png"
                                    onchange="previewPhoto(event)"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                >
                                <p class="mt-1 text-xs text-gray-500">Accepted formats: JPG, PNG. Maximum file size: 2MB. Leave empty to keep current photo.</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="admission_number" class="block text-sm font-semibold text-gray-700 mb-2">
                            Admission Number
                        </label>
                        <input 
                            type="text" 
                            id="admission_number" 
                            value="{{ $student->admission_number ?? 'N/A' }}"
                            readonly
                            disabled
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed"
                        >
                        <p class="mt-1 text-xs text-gray-500">Admission number cannot be changed</p>
                    </div>

                    <div>
                        <label for="first_name" class="block text-sm font-semibold text-gray-700 mb-2">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="first_name" 
                            name="first_name" 
                            value="{{ old('first_name', $student->first_name) }}"
                            required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            placeholder="Enter first name"
                        >
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="middle_name" class="block text-sm font-semibold text-gray-700 mb-2">
                            Middle Name
                        </label>
                        <input 
                            type="text" 
                            id="middle_name" 
                            name="middle_name" 
                            value="{{ old('middle_name', $student->middle_name) }}"
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
                            value="{{ old('last_name', $student->last_name) }}"
                            required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            placeholder="Enter last name"
                        >
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="date_of_birth" class="block text-sm font-semibold text-gray-700 mb-2">
                            Date of Birth <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            id="date_of_birth" 
                            name="date_of_birth" 
                            value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}"
                            required
                            max="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        >
                        @error('date_of_birth')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="gender" class="block text-sm font-semibold text-gray-700 mb-2">
                            Gender <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="gender" 
                            name="gender" 
                            required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        >
                            <option value="">Select gender...</option>
                            <option value="male" {{ old('gender', $student->gender) === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $student->gender) === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', $student->gender) === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
                            value="{{ old('phone', $student->phone) }}"
                            required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            placeholder="+254 700 000 000"
                        >
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            Email Address
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email', $student->email) }}"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            placeholder="student@example.com"
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">
                            Address
                        </label>
                        <textarea 
                            id="address" 
                            name="address" 
                            rows="3"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            placeholder="Enter full address (optional)"
                        >{{ old('address', $student->address) }}</textarea>
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
                            required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        >
                            <option value="">Select level...</option>
                            <option value="Primary" {{ old('level_of_education', $student->level_of_education) === 'Primary' ? 'selected' : '' }}>Primary</option>
                            <option value="High School" {{ old('level_of_education', $student->level_of_education) === 'High School' ? 'selected' : '' }}>High School</option>
                            <option value="Diploma" {{ old('level_of_education', $student->level_of_education) === 'Diploma' ? 'selected' : '' }}>Diploma</option>
                            <option value="Bachelor" {{ old('level_of_education', $student->level_of_education) === 'Bachelor' ? 'selected' : '' }}>Bachelor</option>
                            <option value="Master" {{ old('level_of_education', $student->level_of_education) === 'Master' ? 'selected' : '' }}>Master</option>
                            <option value="PhD" {{ old('level_of_education', $student->level_of_education) === 'PhD' ? 'selected' : '' }}>PhD</option>
                        </select>
                        @error('level_of_education')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nationality" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nationality <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="nationality" 
                            name="nationality" 
                            value="{{ old('nationality', $student->nationality) }}"
                            required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            placeholder="e.g., Kenyan"
                        >
                        @error('nationality')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="id_passport_number" class="block text-sm font-semibold text-gray-700 mb-2">
                            ID / Passport Number <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="id_passport_number" 
                            name="id_passport_number" 
                            value="{{ old('id_passport_number', $student->id_passport_number) }}"
                            required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            placeholder="Enter ID or Passport number"
                        >
                        @error('id_passport_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
                            value="{{ old('next_of_kin_name', $student->next_of_kin_name) }}"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            placeholder="Enter full name (optional)"
                        >
                        @error('next_of_kin_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="next_of_kin_mobile" class="block text-sm font-semibold text-gray-700 mb-2">
                            Guardian Mobile Number
                        </label>
                        <input 
                            type="tel" 
                            id="next_of_kin_mobile" 
                            name="next_of_kin_mobile" 
                            value="{{ old('next_of_kin_mobile', $student->next_of_kin_mobile) }}"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            placeholder="+254 700 000 000 (optional)"
                        >
                        @error('next_of_kin_mobile')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
                            required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        >
                            <option value="active" {{ old('status', $student->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $student->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="graduated" {{ old('status', $student->status) === 'graduated' ? 'selected' : '' }}>Graduated</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t border-gray-200">
                <a 
                    href="{{ route('students.show', $student->id) }}" 
                    class="px-8 py-3 border-2 border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition-colors text-center"
                >
                    Cancel
                </a>
                <button 
                    type="submit" 
                    class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg hover:shadow-xl"
                >
                    Update Student Information
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewPhoto(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('photo-preview');
            const previewImg = document.getElementById('photo-preview-img');
            const placeholder = document.getElementById('photo-placeholder');
            
            if (preview && previewImg) {
                previewImg.src = e.target.result;
                preview.classList.remove('hidden');
                if (placeholder) {
                    placeholder.classList.add('hidden');
                }
            }
        };
        reader.readAsDataURL(file);
    }
}
</script>
@endsection
