@extends('layouts.app')

@section('title', 'Process Payment')
@section('page-title', 'Process Payment')

@section('content')
<div class="max-w-4xl mx-auto" x-data="billingForm()">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">New Payment</h2>

        <form @submit.prevent="submitForm" method="POST" action="{{ route('billing.store') }}">
            @csrf

            <!-- Student Selection -->
            <div class="mb-6">
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
                        :value="selectedStudentName"
                        readonly
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
            <div class="mb-6">
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
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">
                            {{ $course->name }} ({{ $course->code }})
                        </option>
                    @endforeach
                </select>
                <p x-show="studentCourses.length === 0 && studentId" class="mt-2 text-sm text-yellow-600">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    This student has no registered courses for the current academic year. Showing all available courses.
                </p>
                <p x-show="studentCourses.length > 0 && studentId" class="mt-2 text-sm text-green-600">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Showing registered courses for this student.
                </p>
            </div>

            <!-- Academic Year (Hidden - Auto-set) -->
            <input 
                type="hidden" 
                id="academic_year" 
                name="academic_year" 
                value="{{ $currentAcademicYear }}"
            >

            <!-- Month Selection -->
            <div class="mb-6">
                <label for="month" class="block text-sm font-medium text-gray-700 mb-2">Billing Month *</label>
                <select 
                    id="month" 
                    name="month" 
                    x-model="selectedMonth"
                    @change="loadMonthCourses"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    @php
                        $months = [
                            'January', 'February', 'March', 'April', 'May', 'June',
                            'July', 'August', 'September', 'October', 'November', 'December'
                        ];
                        $currentMonthName = now()->format('F');
                        $currentMonthValue = $currentMonthName . ' ' . $currentYear;
                        // Generate months for current year and next year
                        $years = [now()->year, now()->year + 1];
                    @endphp
                    @foreach($years as $year)
                        @foreach($months as $month)
                            @php
                                $monthValue = $month . ' ' . $year;
                                $isSelected = ($month === $currentMonthName && $year === $currentYear);
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

            <!-- Agreed Amount -->
            <div class="mb-6">
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

            <!-- Discount Amount -->
            <div class="mb-6" hidden>
                <label for="discount_amount" class="block text-sm font-medium text-gray-700 mb-2">
                    Discount Amount (KES) (Optional)
                </label>
                <input 
                    type="number" 
                    id="discount_amount" 
                    name="discount_amount" 
                    x-model="discountAmount"
                    @input="calculateBalance"
                    step="0.01"
                    min="0"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg font-semibold"
                    placeholder="0.00"
                >
                <p class="mt-2 text-sm text-gray-500">Enter any discount applied to the agreed amount</p>
            </div>

            <!-- Amount Paid -->
            <div class="mb-6">
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
            <div class="mb-6">
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

            <!-- Notes -->
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                <textarea 
                    id="notes" 
                    name="notes" 
                    rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Additional notes..."
                ></textarea>
            </div>

            <!-- Total Amount Due Display -->
            <div x-show="studentId && (studentOverallBalance > 0 || parseFloat(agreedAmount) > 0)" class="mb-6 p-4 bg-purple-50 rounded-lg border border-purple-200">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Summary for this Payment</h3>
                <div x-show="studentOverallBalance > 0" class="flex justify-between items-center mb-2">
                    <span class="text-gray-600">Overall Outstanding Balance:</span>
                    <span class="font-bold text-orange-700" x-text="'KES ' + studentOverallBalance.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
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
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Payment Summary</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Due for Transaction:</span>
                        <span class="font-semibold" x-text="'KES ' + totalAmountDueForPayment.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Amount Paid:</span>
                        <span class="font-semibold" x-text="'KES ' + (amountPaid ? parseFloat(amountPaid).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00')"></span>
                    </div>
                    <div x-show="balance > 0" class="flex justify-between pt-2 border-t border-gray-300">
                        <span class="text-gray-600">Outstanding Balance:</span>
                        <span class="font-bold text-orange-600" x-text="'KES ' + balance.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                    </div>
                    <div x-show="balance === 0 && agreedAmount && amountPaid" class="flex justify-between pt-2 border-t border-gray-300">
                        <span class="text-gray-600">Status:</span>
                        <span class="font-bold text-green-600">Fully Paid</span>
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
                    x-bind:disabled="!studentId || !courseId || isNaN(parseFloat(agreedAmount)) || parseFloat(agreedAmount) <= 0 || isNaN(parseFloat(amountPaid)) || parseFloat(amountPaid) <= 0 || parseFloat(agreedAmount) < parseFloat(amountPaid)"
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
        
        courseInfo: null,
        studentCourses: [],
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
            if (this.studentId) {
                this.loadStudentInfo();
            }
        },
        
        async loadStudentInfo() {
            if (!this.studentId) {
                this.studentCourses = [];
                this.courseId = '';
                this.courseInfo = null;
                this.selectedStudentName = '';
                this.studentOverallBalance = 0; // Reset total balance
                this.studentCourseBalances = []; // Reset detailed balances
                this.totalAmountDueForPayment = 0; // Reset total amount due
                // Show all courses when no student is selected
                this.$nextTick(() => {
                    const select = this.$refs.courseSelect;
                    if (select) {
                        Array.from(select.options).forEach(option => {
                            if (option.value !== '') {
                                option.style.display = '';
                            }
                        });
                    }
                });
                return;
            }
            
            // Get selected month for filtering
            const monthSelect = document.getElementById('month');
            const selectedMonth = monthSelect ? monthSelect.value : null;
            
            try {
                // Fetch all courses (registered and non-registered) for the student
                const coursesUrl = `/billing/student/${this.studentId}/courses${selectedMonth ? '?month=' + encodeURIComponent(selectedMonth) : ''}`;
                const coursesResponse = await fetch(coursesUrl);
                const coursesData = await coursesResponse.json();
                this.studentCourses = coursesData;
                
                // Fetch overall balance for the student
                const overallBalanceUrl = `/billing/student/${this.studentId}/overall-balance`;
                const overallBalanceResponse = await fetch(overallBalanceUrl);
                const overallBalanceData = await overallBalanceResponse.json();

                this.studentOverallBalance = parseFloat(overallBalanceData.total_outstanding_balance || 0);
                this.studentCourseBalances = overallBalanceData.course_balances || [];
                
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
                
                // Show all courses (students can pay for any course in any month)
                // But we'll keep the studentCourses array for reference
                this.$nextTick(() => {
                    const select = this.$refs.courseSelect;
                    if (select) {
                        // Show all courses - students can pay for any course in any month
                        Array.from(select.options).forEach(option => {
                            if (option.value !== '') {
                                option.style.display = '';
                            }
                        });
                    }
                });
            } catch (error) {
                console.error('Error loading student courses:', error);
                this.studentCourses = [];
            }
        },
        
        async loadMonthCourses() {
            // Reload courses when month changes
            if (this.studentId) {
                await this.loadStudentInfo();
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
            const paid = parseFloat(this.amountPaid) || 0;

            // Automatically calculate discount if agreed amount is less than base price
            if (agreed < base) {
                this.discountAmount = base - agreed;
            } else {
                this.discountAmount = 0;
            }

            // Update total amount due based on current agreedAmount input
            this.totalAmountDueForPayment = this.studentOverallBalance + parseFloat(this.agreedAmount || 0);

            // Calculate balance after applying discount and considering the total amount due
            const totalPayable = this.totalAmountDueForPayment;
            this.balance = Math.max(0, totalPayable - paid);
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

