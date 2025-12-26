@extends('layouts.app')

@section('title', 'Reports Module')
@section('page-title', 'Reports Module')

@section('content')
<div class="max-w-6xl mx-auto" x-data="reportsModule()">
    <div class="bg-white rounded-xl shadow-lg p-8">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Reports Module</h2>
            <p class="text-gray-600">Generate and download reports in Excel format</p>
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
            <a 
                :href="buildReportUrl('{{ route('reports.export-students-registered') }}')"
                class="group bg-gradient-to-br from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 rounded-xl shadow-lg p-6 text-center transition-all transform hover:scale-105 hover:shadow-2xl"
            >
                <div class="mb-4">
                    <svg class="w-14 h-14 mx-auto text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Students Registered</h3>
                <p class="text-blue-100 text-xs">Complete list of all registered students</p>
            </a>

            <!-- Balances Report -->
            <a 
                :href="buildReportUrl('{{ route('reports.export-balances') }}')"
                class="group bg-gradient-to-br from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 rounded-xl shadow-lg p-6 text-center transition-all transform hover:scale-105 hover:shadow-2xl"
            >
                <div class="mb-4">
                    <svg class="w-14 h-14 mx-auto text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Balances Report</h3>
                <p class="text-green-100 text-xs">Student balances and payment information</p>
            </a>

            <!-- Fee Payment Report -->
            <a 
                :href="buildReportUrl('{{ route('reports.export-payments') }}')"
                class="group bg-gradient-to-br from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 rounded-xl shadow-lg p-6 text-center transition-all transform hover:scale-105 hover:shadow-2xl"
            >
                <div class="mb-4">
                    <svg class="w-14 h-14 mx-auto text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Fee Payment Report</h3>
                <p class="text-purple-100 text-xs">All fee payments with details</p>
            </a>

            <!-- Expenses Report -->
            <a 
                :href="buildReportUrl('{{ route('reports.export-expenses') }}')"
                class="group bg-gradient-to-br from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 rounded-xl shadow-lg p-6 text-center transition-all transform hover:scale-105 hover:shadow-2xl"
            >
                <div class="mb-4">
                    <svg class="w-14 h-14 mx-auto text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Expenses Report</h3>
                <p class="text-red-100 text-xs">All expenses and costs</p>
            </a>

            <!-- Full Financial Report -->
            <a 
                :href="buildReportUrl('{{ route('reports.export') }}')"
                class="group bg-gradient-to-br from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 rounded-xl shadow-lg p-6 text-center transition-all transform hover:scale-105 hover:shadow-2xl"
            >
                <div class="mb-4">
                    <svg class="w-14 h-14 mx-auto text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Full Financial Report</h3>
                <p class="text-indigo-100 text-xs">Complete financial summary with all data</p>
            </a>

            <!-- Course Registrations Report -->
            <a 
                :href="buildReportUrl('{{ route('reports.export-course-registrations') }}')"
                class="group bg-gradient-to-br from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 rounded-xl shadow-lg p-6 text-center transition-all transform hover:scale-105 hover:shadow-2xl"
            >
                <div class="mb-4">
                    <svg class="w-14 h-14 mx-auto text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Course Registrations</h3>
                <p class="text-teal-100 text-xs">Student course registration records</p>
            </a>

            <!-- Bank Deposits Report -->
            <a 
                :href="buildReportUrl('{{ route('reports.export-bank-deposits') }}')"
                class="group bg-gradient-to-br from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 rounded-xl shadow-lg p-6 text-center transition-all transform hover:scale-105 hover:shadow-2xl"
            >
                <div class="mb-4">
                    <svg class="w-14 h-14 mx-auto text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Bank Deposits</h3>
                <p class="text-yellow-100 text-xs">All bank deposit transactions</p>
            </a>

            <!-- Receipts Report -->
            <a 
                :href="buildReportUrl('{{ route('reports.export-receipts') }}')"
                class="group bg-gradient-to-br from-pink-500 to-pink-600 hover:from-pink-600 hover:to-pink-700 rounded-xl shadow-lg p-6 text-center transition-all transform hover:scale-105 hover:shadow-2xl"
            >
                <div class="mb-4">
                    <svg class="w-14 h-14 mx-auto text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Receipts Report</h3>
                <p class="text-pink-100 text-xs">All issued receipts with details</p>
            </a>
        </div>

        <div class="mt-8 text-center">
            <p class="text-sm text-gray-500">
                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Reports will be automatically downloaded in Excel (.xlsx) format
            </p>
        </div>
    </div>
</div>

<script>
function reportsModule() {
    return {
        dateFrom: '',
        dateTo: '',
        
        init() {
            // Set default dates to current month if needed
            // For now, leave empty to show all records by default
        },
        
        buildReportUrl(baseUrl) {
            const params = new URLSearchParams();
            
            if (this.dateFrom) {
                params.append('date_from', this.dateFrom);
            }
            
            if (this.dateTo) {
                params.append('date_to', this.dateTo);
            }
            
            const queryString = params.toString();
            return queryString ? `${baseUrl}?${queryString}` : baseUrl;
        },
        
        clearDates() {
            this.dateFrom = '';
            this.dateTo = '';
        }
    }
}
</script>
@endsection

