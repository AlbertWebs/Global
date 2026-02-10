@extends('layouts.app')

@section('title', 'Reports Module')
@section('page-title', 'Reports Module')

@section('content')
<div class="max-w-6xl mx-auto" x-data="reportsModule()">
    <div class="bg-white rounded-xl shadow-lg p-8">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Reports Module</h2>
            <p class="text-gray-600">Generate and download reports in Excel or PDF format</p>
        </div>

        <!-- Date Range Selection -->
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg p-6 mb-8 border-2 border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Select Date Range (Optional)
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                    <input 
                        type="date" 
                        id="date_from" 
                        x-model="dateFrom"
                        class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                    <input 
                        type="date" 
                        id="date_to" 
                        x-model="dateTo"
                        class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>
                <div class="flex items-end">
                    <button 
                        @click="clearDates()"
                        class="w-full px-4 py-2.5 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors font-medium"
                    >
                        Clear Dates
                    </button>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-3">
                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Leave dates empty to include all records. Date filtering applies to reports that support it.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Students Registered Report -->
            <button 
                @click="openFormatModal('{{ route('reports.export-students-registered') }}', 'Students Registered')"
                class="group bg-gradient-to-br from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 rounded-xl shadow-lg p-6 text-center transition-all transform hover:scale-105 hover:shadow-2xl cursor-pointer"
            >
                <div class="mb-4">
                    <svg class="w-14 h-14 mx-auto text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Students Registered</h3>
                <p class="text-blue-100 text-xs">Complete list of all registered students</p>
            </button>

            <!-- Balances Report -->
            <button 
                @click="openFormatModal('{{ route('reports.export-balances') }}', 'Balances Report')"
                class="group bg-gradient-to-br from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 rounded-xl shadow-lg p-6 text-center transition-all transform hover:scale-105 hover:shadow-2xl cursor-pointer"
            >
                <div class="mb-4">
                    <svg class="w-14 h-14 mx-auto text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Balances Report</h3>
                <p class="text-green-100 text-xs">Student balances and payment information</p>
            </button>

            <!-- Fee Payment Report -->
            <button 
                @click="openFormatModal('{{ route('reports.export-payments') }}', 'Fee Payment Report')"
                class="group bg-gradient-to-br from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 rounded-xl shadow-lg p-6 text-center transition-all transform hover:scale-105 hover:shadow-2xl cursor-pointer"
            >
                <div class="mb-4">
                    <svg class="w-14 h-14 mx-auto text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Fee Payment Report</h3>
                <p class="text-purple-100 text-xs">All fee payments with details</p>
            </button>

            <!-- Expenses Report -->
            <button 
                @click="openFormatModal('{{ route('reports.export-expenses') }}', 'Expenses Report')"
                class="group bg-gradient-to-br from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 rounded-xl shadow-lg p-6 text-center transition-all transform hover:scale-105 hover:shadow-2xl cursor-pointer"
            >
                <div class="mb-4">
                    <svg class="w-14 h-14 mx-auto text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Expenses Report</h3>
                <p class="text-red-100 text-xs">All expenses and costs</p>
            </button>

            <!-- Full Financial Report -->
            <button 
                @click="openFormatModal('{{ route('reports.export') }}', 'Full Financial Report')"
                class="group bg-gradient-to-br from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 rounded-xl shadow-lg p-6 text-center transition-all transform hover:scale-105 hover:shadow-2xl cursor-pointer"
            >
                <div class="mb-4">
                    <svg class="w-14 h-14 mx-auto text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Full Financial Report</h3>
                <p class="text-indigo-100 text-xs">Complete financial summary with all data</p>
            </button>

            <!-- Course Registrations Report -->
            <button 
                @click="openFormatModal('{{ route('reports.export-course-registrations') }}', 'Course Registrations Report')"
                class="group bg-gradient-to-br from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 rounded-xl shadow-lg p-6 text-center transition-all transform hover:scale-105 hover:shadow-2xl cursor-pointer"
            >
                <div class="mb-4">
                    <svg class="w-14 h-14 mx-auto text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Course Registrations</h3>
                <p class="text-teal-100 text-xs">Student course registration records</p>
            </button>

            <!-- Bank Deposits Report -->
            <button 
                @click="openFormatModal('{{ route('reports.export-bank-deposits') }}', 'Bank Deposits Report')"
                class="group bg-gradient-to-br from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 rounded-xl shadow-lg p-6 text-center transition-all transform hover:scale-105 hover:shadow-2xl cursor-pointer"
            >
                <div class="mb-4">
                    <svg class="w-14 h-14 mx-auto text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Bank Deposits</h3>
                <p class="text-yellow-100 text-xs">All bank deposit transactions</p>
            </button>

            <!-- M-Pesa Payments Report -->
            <button 
                @click="openFormatModal('{{ route('reports.export-mpesa-payments') }}', 'M-Pesa Payments Report')"
                class="group bg-gradient-to-br from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 rounded-xl shadow-lg p-6 text-center transition-all transform hover:scale-105 hover:shadow-2xl cursor-pointer"
            >
                <div class="mb-4">
                    <svg class="w-14 h-14 mx-auto text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">M-Pesa Payments</h3>
                <p class="text-emerald-100 text-xs">All M-Pesa payment transactions</p>
            </button>

            <!-- Cash Payments Report -->
            <button 
                @click="openFormatModal('{{ route('reports.export-cash-payments') }}', 'Cash Payments Report')"
                class="group bg-gradient-to-br from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 rounded-xl shadow-lg p-6 text-center transition-all transform hover:scale-105 hover:shadow-2xl cursor-pointer"
            >
                <div class="mb-4">
                    <svg class="w-14 h-14 mx-auto text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Cash Payments</h3>
                <p class="text-amber-100 text-xs">All cash payment transactions</p>
            </button>
        </div>

        <div class="mt-8 text-center">
            <p class="text-sm text-gray-500">
                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Click any report to choose between Excel (.xlsx) or PDF (.pdf) format
            </p>
        </div>
    </div>

    <!-- Format Selection Modal -->
    <div 
        x-show="showFormatModal" 
        x-cloak
        @click.away="closeFormatModal()"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full mx-4"
            @click.stop
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
        >
            <div class="text-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Select Export Format</h3>
                <p class="text-gray-600" x-text="selectedReportName"></p>
            </div>
            
            <div class="grid grid-cols-1 gap-4 mb-6">
                <button
                    @click="exportReport('excel')"
                    class="flex items-center justify-center px-6 py-4 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-lg font-semibold transition-all transform hover:scale-105 shadow-lg hover:shadow-xl"
                >
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export as Excel (.xlsx)
                </button>
                
                <button
                    @click="exportReport('pdf')"
                    class="flex items-center justify-center px-6 py-4 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-lg font-semibold transition-all transform hover:scale-105 shadow-lg hover:shadow-xl"
                >
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Export as PDF (.pdf)
                </button>
            </div>
            
            <button
                @click="closeFormatModal()"
                class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium"
            >
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
function reportsModule() {
    return {
        dateFrom: '',
        dateTo: '',
        showFormatModal: false,
        selectedReportUrl: '',
        selectedReportName: '',
        
        init() {
            // Set default dates to current month if needed
            // For now, leave empty to show all records by default
        },
        
        buildReportUrl(baseUrl, format = 'excel') {
            const params = new URLSearchParams();
            
            if (this.dateFrom) {
                params.append('date_from', this.dateFrom);
            }
            
            if (this.dateTo) {
                params.append('date_to', this.dateTo);
            }
            
            params.append('format', format);
            
            const queryString = params.toString();
            return queryString ? `${baseUrl}?${queryString}` : `${baseUrl}?format=${format}`;
        },
        
        openFormatModal(reportUrl, reportName) {
            this.selectedReportUrl = reportUrl;
            this.selectedReportName = reportName;
            this.showFormatModal = true;
        },
        
        closeFormatModal() {
            this.showFormatModal = false;
            this.selectedReportUrl = '';
            this.selectedReportName = '';
        },
        
        exportReport(format) {
            const url = this.buildReportUrl(this.selectedReportUrl, format);
            window.location.href = url;
            this.closeFormatModal();
        },
        
        clearDates() {
            this.dateFrom = '';
            this.dateTo = '';
        }
    }
}
</script>
@endsection

