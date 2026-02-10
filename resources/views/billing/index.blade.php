@extends('layouts.app')

@section('title', 'Process Payment')
@section('page-title', 'Process Payment')

@section('content')
<div class="max-w-4xl mx-auto" x-data="billingForm()">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">New Payment</h2>

        <form @submit.prevent="submitForm" method="POST" action="{{ route('billing.store') }}" id="billingForm">
            @csrf

            <!-- Student & Course Selection (Side-by-side) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Student Selection -->
                <div class="mb-0">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Student *</label>
                    <div class="relative" x-data="{ studentSearch: '', showStudentDropdown: false }">
                        <input 
                            type="text" 
                            x-model="studentSearch" 
                            @focus="showStudentDropdown = true"
                            @click.away="showStudentDropdown = false"
                            @input="showStudentDropdown = true"
                            placeholder="Search student by name or number..."
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                        <input type="hidden" name="student_id" x-model="studentId" required>
                        <div x-show="showStudentDropdown" x-cloak class="absolute z-10 w-full mt-1 bg-white border-2 border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            <div class="p-2">
                                @foreach($students as $student)
                                    <div 
                                        class="px-3 py-2 hover:bg-blue-50 cursor-pointer rounded"
                                        x-show="!studentSearch || ('{{ strtolower($student->full_name . ' ' . $student->student_number) }}').includes(studentSearch.toLowerCase())"
                                        @click="studentId = '{{ $student->id }}'; selectedStudentName = '{{ $student->full_name }} ({{ $student->student_number }})'; studentSearch = ''; showStudentDropdown = false; loadStudentInfo();"
                                    >
                                        <div class="font-medium text-gray-900">{{ $student->full_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $student->student_number }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div x-show="studentId" class="mt-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                            <span x-text="selectedStudentName"></span>
                            <button type="button" @click="studentId = ''; selectedStudentName = ''; loadStudentInfo();" class="ml-2 text-blue-600 hover:text-blue-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </span>
                    </div>
                </div>

                <!-- Course Selection -->
                <div class="mb-0">
                    <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">Select Course *</label>
                    <select 
                        id="course_id" 
                        name="course_id" 
                        x-model="courseId"
                        @change="loadCourseInfo"
                        x-ref="courseSelect"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Choose a course...</option>
                        <template x-for="course in registeredCourses" :key="course.id">
                            <option :value="course.id" x-text="`${course.name} (${course.code})`"></option>
                        </template>
                    </select>
                </div>
            </div>

            <!-- Academic Year (Hidden - Auto-set) -->
            <input 
                type="hidden" 
                id="academic_year" 
                name="academic_year" 
                value="{{ $currentAcademicYear }}"
            >

            <!-- Month Selection -->
            <div class="mb-6" hidden>
                <label for="month" class="block text-sm font-medium text-gray-700 mb-2">Billing Month *</label>
                <select 
                    id="month" 
                    name="month" 
                    x-model="selectedMonth"
                    @change="loadStudentInfo"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    @php
                        $months = [
                            'January', 'February', 'March', 'April', 'May', 'June',
                            'July', 'August', 'September', 'October', 'November', 'December'
                        ];
                        $currentMonthName = now()->format('F');
                        $currentMonthValue = $currentMonthName . ' ' . now()->year;
                        // Generate months for current year and next year
                        $years = [now()->year, now()->year + 1];
                    @endphp
                    @foreach($years as $year)
                        @foreach($months as $month)
                            @php
                                $monthValue = $month . ' ' . $year;
                                $isSelected = ($month === $currentMonthName && $year === now()->year);
                            @endphp
                            <option value="{{ $monthValue }}" {{ $isSelected ? 'selected' : '' }}>
                                {{ $month }} {{ $year }}
                            </option>
                        @endforeach
                    @endforeach
                </select>
                <p class="mt-1 text-sm text-gray-500">Select the month this payment is for. Students can pay for the same course in different months.</p>
            </div>

            <!-- Year (hidden) -->
            <!-- Year will be extracted from month selection via JavaScript -->
            <input type="hidden" name="year" :value="getYearFromMonth(selectedMonth)" value="{{ $currentYear }}">

            <!-- Course Info Display (No Price for Cashier) -->
            <div x-show="courseInfo" class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Selected Course</p>
                        <p class="text-lg font-semibold text-gray-900" x-text="courseInfo?.name"></p>
                        <p class="text-sm text-gray-500" x-text="courseInfo?.code"></p>
                    </div>
                    @if(auth()->user()->isSuperAdmin())
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Base Price</p>
                        <p class="text-xl font-bold text-blue-600" x-text="'KES ' + (courseInfo?.base_price ? parseFloat(courseInfo.base_price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00')"></p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Payment Details: Agreed Amount, Amount Paid, and Payment Method (Side-by-side) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Agreed Amount -->
                <div class="mb-0">
                    <label for="agreed_amount" class="block text-sm font-medium text-gray-700 mb-2">
                        Agreed Amount (KES) *
                    </label>
                    <input 
                        type="number" 
                        id="agreed_amount" 
                        name="agreed_amount" 
                        x-model="agreedAmount"
                        @input="calculateBalance"
                        step="0.01"
                        min="0"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg font-semibold"
                        placeholder="0.00"
                    >
                    <p class="mt-2 text-sm text-gray-500">Enter the full amount agreed to be paid</p>
                </div>

                <!-- Amount Paid -->
                <div class="mb-0">
                    <label for="amount_paid" class="block text-sm font-medium text-gray-700 mb-2">
                        Amount Paid (KES) *
                    </label>
                    <input 
                        type="number" 
                        id="amount_paid" 
                        name="amount_paid" 
                        x-model="amountPaid"
                        @input="calculateBalance"
                        step="0.01"
                        min="0"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg font-semibold"
                        placeholder="0.00"
                    >
                    <p class="mt-2 text-sm text-gray-500">Enter the amount being paid now</p>
                </div>

                <!-- Payment Method -->
                <div class="mb-0">
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">Payment Method *</label>
                    <select 
                        id="payment_method" 
                        name="payment_method" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="mpesa">M-Pesa</option>
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                    </select>
                </div>
            </div>

            <!-- Notes -->
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                <textarea 
                    id="notes" 
                    name="notes" 
                    rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="transaction id"
                ></textarea>
            </div>

            <!-- Total Amount Due Display -->
            <div x-show="studentId && (studentOverallBalance > 0 || parseFloat(agreedAmount) > 0)" class="mb-6 p-4 bg-purple-50 rounded-lg border border-purple-200">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Summary for this Payment</h3>
                <div x-show="studentOverallBalance > 0" class="flex justify-between items-center mb-2">
                    <span class="text-gray-600">Overall Outstanding Balance:</span>
                    <span class="font-bold text-orange-700" x-text="'KES ' + parseFloat(studentOverallBalance || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                </div>
                <div x-show="walletBalance > 0" class="flex justify-between items-center mb-2">
                    <span class="text-gray-600">Wallet Balance:</span>
                    <span class="font-bold text-green-700" x-text="'KES ' + walletBalance.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                </div>
                <div x-show="parseFloat(agreedAmount) > 0" class="flex justify-between items-center mb-2" :class="{'mt-2 border-t pt-2': studentOverallBalance > 0}">
                    <span class="text-gray-600">Agreed Amount for Selected Course:</span>
                    <span class="font-bold text-blue-700" x-text="'KES ' + parseFloat(agreedAmount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t-2 border-gray-300 mt-3">
                    <span class="text-xl font-bold text-gray-900">Total Amount Due:</span>
                    <span class="text-3xl font-bold text-purple-700" x-text="'KES ' + totalAmountDueForPayment.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                </div>
                <p class="mt-2 text-sm text-gray-500">This is the total amount expected for this transaction, including any outstanding balances and the agreed amount for the selected course.</p>
            </div>

            <!-- Payment Summary -->
            <div x-show="agreedAmount && amountPaid" class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Payment Summary for Selected Course</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Agreed Amount:</span>
                        <span class="font-semibold" x-text="'KES ' + parseFloat(agreedAmount || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Cash/Bank Payment:</span>
                        <span class="font-semibold" x-text="'KES ' + (amountPaid ? parseFloat(amountPaid).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00')"></span>
                    </div>
                    <div x-show="usedWalletAmount > 0" class="flex justify-between">
                        <span class="text-gray-600">Amount from Wallet:</span>
                        <span class="font-semibold text-green-600" x-text="'KES ' + usedWalletAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-gray-300">
                        <span class="text-gray-600 font-semibold">Total Payment:</span>
                        <span class="font-bold text-blue-600" x-text="'KES ' + (parseFloat(amountPaid || 0) + parseFloat(usedWalletAmount || 0)).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                    </div>
                    <div x-show="balance > 0" class="flex justify-between pt-2 border-t-2 border-orange-300">
                        <span class="text-gray-700 font-semibold">Balance Due:</span>
                        <span class="font-bold text-orange-600" x-text="'KES ' + balance.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                    </div>
                    <div x-show="balance === 0 && (parseFloat(amountPaid || 0) + parseFloat(usedWalletAmount || 0)) === parseFloat(agreedAmount || 0)" class="flex justify-between pt-2 border-t-2 border-green-300">
                        <span class="text-gray-700 font-semibold">Balance:</span>
                        <span class="font-bold text-green-600">KES 0.00 (Fully Paid)</span>
                    </div>
                    <div x-show="balance < 0" class="flex justify-between pt-2 border-t-2 border-green-300">
                        <span class="text-gray-700 font-semibold">Credit (will be added to wallet):</span>
                        <span class="font-bold text-green-600" x-text="'KES ' + Math.abs(balance).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('dashboard') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button 
                    type="submit"
                    x-init="$watch('studentId', () => {}); $watch('courseId', () => {}); $watch('agreedAmount', () => {}); $watch('amountPaid', () => {});"
                    x-bind:disabled="!studentId || !courseId || isNaN(parseFloat(agreedAmount)) || parseFloat(agreedAmount) <= 0 || isNaN(parseFloat(amountPaid)) || parseFloat(amountPaid) <= 0"
                    class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg hover:shadow-xl"
                >
                    Process Payment & Generate Receipt
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function billingForm() {
    return {
        studentId: '{{ $selectedStudentId ?? '' }}',
        selectedStudentName: '{{ $selectedStudent ? $selectedStudent->full_name . " (" . $selectedStudent->student_number . ")" : "" }}',
        courseId: '',
        agreedAmount: '',
        amountPaid: '',
        discountAmount: '', // New property for discount amount
        balance: 0,
        basePrice: 0, // New property for course base price
        studentOverallBalance: 0, // New property for student's overall outstanding balance
        studentCourseBalances: [], // New property for detailed course balances
        totalAmountDueForPayment: 0, // Total amount due: overall balance + agreedAmount for selected course
        walletBalance: {{ $walletBalance ?? 0 }}, // Initialize wallet balance from backend
        usedWalletAmount: 0,

        registeredCourses: [], // Will hold courses registered by the selected student

        selectedMonth: '{{ $currentMonthName }} {{ $currentYear }}',

        init() {
            // Ensure current month is selected by default
            const monthSelect = document.getElementById('month');
            if (monthSelect && !this.selectedMonth) {
                const currentMonthOption = monthSelect.querySelector('option[selected]');
                if (currentMonthOption) {
                    this.selectedMonth = currentMonthOption.value;
                }
            }

            // If student is pre-selected, trigger any necessary actions
            // Use $nextTick to ensure Alpine is fully initialized
            this.$nextTick(() => {
                if (this.studentId) {
                    this.loadStudentInfo();
                }
            });
        },

        async loadStudentInfo() {
            if (!this.studentId) {
                this.registeredCourses = []; // Clear registered courses
                this.courseId = '';
                this.courseInfo = null;
                this.selectedStudentName = '';
                this.studentOverallBalance = 0; // Reset total balance
                this.studentCourseBalances = []; // Reset detailed balances
                this.totalAmountDueForPayment = 0; // Reset total amount due
                this.walletBalance = 0; // Reset wallet balance
                return;
            }

            try {
                // Fetch only registered courses for the student
                const registeredCoursesUrl = `/api/students/${this.studentId}/registered-courses`;
                const registeredCoursesResponse = await fetch(registeredCoursesUrl);
                const registeredCoursesData = await registeredCoursesResponse.json();
                this.registeredCourses = registeredCoursesData;

                // Fetch overall balance for the student
                const overallBalanceUrl = `/billing/student/${this.studentId}/overall-balance`;
                const overallBalanceResponse = await fetch(overallBalanceUrl);
                const overallBalanceData = await overallBalanceResponse.json();

                this.studentOverallBalance = parseFloat(overallBalanceData.total_outstanding_balance || 0);
                this.studentCourseBalances = overallBalanceData.course_balances || [];
                
                // Update total amount due with the outstanding balance
                this.totalAmountDueForPayment = this.studentOverallBalance + parseFloat(this.agreedAmount || 0);

                // Fetch wallet balance for the student
                const walletUrl = `/api/students/${this.studentId}/wallet-balance`;
                const walletResponse = await fetch(walletUrl);
                const walletData = await walletResponse.json();
                this.walletBalance = parseFloat(walletData.balance || 0);

                // Reset agreedAmount when student changes to prevent carry-over from previous selections.
                // It will be populated when a course is selected.
                this.agreedAmount = '';

                // Reset course selection when student changes
                this.courseId = '';
                this.courseInfo = null;
                this.basePrice = 0;
                this.amountPaid = '';
                this.balance = 0;
                this.discountAmount = 0;

                this.calculateBalance(); // Recalculate balance after loading student info

            } catch (error) {
                console.error('Error loading student courses or balance:', error);
                this.registeredCourses = [];
                this.studentOverallBalance = 0;
                this.studentCourseBalances = [];
                this.walletBalance = 0;
            }
        },

        async loadCourseInfo() {
            // Reset relevant fields when course changes
            this.agreedAmount = '';
            this.amountPaid = '';
            this.discountAmount = '';
            this.balance = 0;
            this.totalAmountDueForPayment = this.studentOverallBalance; // Initially, total due is overall balance

            if (!this.courseId) {
                this.courseInfo = null;
                this.calculateBalance(); // Recalculate if no course is selected
                return;
            }

            try {
                const response = await fetch(`/billing/course/${this.courseId}`);
                const data = await response.json();
                this.courseInfo = data;
                this.basePrice = parseFloat(data.base_price || 0); // Set basePrice here

                let defaultAgreedAmount = 0;
                @if(auth()->user()->isSuperAdmin())
                if (data.base_price) {
                    defaultAgreedAmount = parseFloat(data.base_price);
                }
                @endif

                // Find if there's an outstanding balance for this specific course
                const specificCourseBalance = this.studentCourseBalances.find(cb => cb.course_id == this.courseId);

                if (specificCourseBalance && specificCourseBalance.outstanding_balance > 0) {
                    // If there's an existing balance for this specific course, pre-fill agreed amount with it
                    this.agreedAmount = specificCourseBalance.outstanding_balance;
                } else if (defaultAgreedAmount > 0) {
                    // If no specific course balance, but the selected course has a default price, pre-fill agreed amount with it
                    this.agreedAmount = defaultAgreedAmount;
                }

                // Update total amount due based on current agreedAmount input
                this.totalAmountDueForPayment = this.studentOverallBalance + parseFloat(this.agreedAmount || 0);
                this.calculateBalance(); // Always recalculate balance after loading course info

            } catch (error) {
                console.error('Error loading course info:', error);
                this.totalAmountDueForPayment = this.studentOverallBalance; // Reset to overall balance on error
                this.calculateBalance();
            }
        },

        calculateBalance() {
            const base = parseFloat(this.basePrice) || 0;
            const agreed = parseFloat(this.agreedAmount) || 0;
            let paid = parseFloat(this.amountPaid) || 0;

            // Automatically calculate discount if agreed amount is less than base price
            if (agreed < base) {
                this.discountAmount = base - agreed;
            } else {
                this.discountAmount = 0;
            }

            // For this payment, we focus on the agreed amount for the selected course
            // Check if there's an existing balance for this specific course
            const specificCourseBalance = this.studentCourseBalances.find(cb => cb.course_id == this.courseId);
            const amountNeededForCourse = specificCourseBalance && specificCourseBalance.outstanding_balance > 0 
                ? specificCourseBalance.outstanding_balance 
                : agreed;

            // Calculate shortfall after cash payment
            const shortfallAfterCash = Math.max(0, amountNeededForCourse - paid);

            // Calculate how much wallet balance to use to cover the shortfall
            let amountFromWallet = 0;
            if (this.walletBalance > 0 && shortfallAfterCash > 0) {
                // Use wallet balance to cover the shortfall
                amountFromWallet = Math.min(this.walletBalance, shortfallAfterCash);
                this.usedWalletAmount = amountFromWallet;
            } else {
                this.usedWalletAmount = 0;
            }

            // Calculate total payment (cash + wallet)
            const totalPayment = paid + amountFromWallet;

            // Calculate balance for this specific course
            // Balance = agreed amount - total payment (cash + wallet)
            // Negative balance means overpayment (credit)
            this.balance = agreed - totalPayment;
        },

        getYearFromMonth(monthString) {
            if (!monthString) return '';
            const parts = monthString.split(' ');
            return parts[1] || '';
        },

        submitForm() {
            // Ensure year is set correctly from month selection
            const yearInput = this.$el.querySelector('input[name="year"]');
            if (yearInput && this.selectedMonth) {
                yearInput.value = this.getYearFromMonth(this.selectedMonth);
            }
            // Form validation happens on server side
            this.$el.submit();
        }
    }
}
</script>
@endsection

@if ($errors->any())
<script>
let errorMessages = '';
@foreach ($errors->all() as $error)
errorMessages += '{{ addslashes($error) }}\n';
@endforeach
alert('Validation Errors:\n' + errorMessages);
</script>
@endif

