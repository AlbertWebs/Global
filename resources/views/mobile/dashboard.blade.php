<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#2563eb">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Mobile Dashboard - Global College</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="manifest" href="{{ asset('manifest.json') }}">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-md mx-auto bg-white min-h-screen" x-data="mobileDashboard()">
        <!-- Header -->
        <header class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4 sticky top-0 z-10 shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold">Global College</h1>
                    <p class="text-sm text-blue-100">Mobile Dashboard</p>
                </div>
                <a href="{{ route('dashboard') }}" class="text-white hover:text-blue-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
            </div>
        </header>

        <!-- Quick Stats -->
        <div class="p-4 space-y-3">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">Quick Overview</h2>
            
            <div class="grid grid-cols-3 gap-3">
                <div class="bg-blue-50 rounded-lg p-3 border border-blue-200">
                    <p class="text-xs text-blue-600 font-medium mb-1">Today</p>
                    <p class="text-lg font-bold text-blue-900">KES {{ number_format($todayPayments, 2) }}</p>
                </div>
                <div class="bg-green-50 rounded-lg p-3 border border-green-200">
                    <p class="text-xs text-green-600 font-medium mb-1">This Week</p>
                    <p class="text-lg font-bold text-green-900">KES {{ number_format($weekPayments, 2) }}</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-3 border border-purple-200">
                    <p class="text-xs text-purple-600 font-medium mb-1">This Month</p>
                    <p class="text-lg font-bold text-purple-900">KES {{ number_format($monthPayments, 2) }}</p>
                </div>
            </div>

            <!-- Current Term Info -->
            <div class="bg-gray-100 rounded-lg p-3 mt-3">
                <p class="text-sm text-gray-600">Current Period</p>
                <p class="text-lg font-semibold text-gray-900">{{ $currentTerm }} - {{ $currentAcademicYear }}</p>
            </div>
        </div>

        <!-- System Health Alerts -->
        @if(count($healthIssues) > 0)
        <div class="px-4 mb-4">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">System Health</h2>
            <div class="space-y-2">
                @foreach($healthIssues as $issue)
                <div class="rounded-lg p-3 border-l-4 
                    @if($issue['type'] === 'error') bg-red-50 border-red-500
                    @elseif($issue['type'] === 'warning') bg-yellow-50 border-yellow-500
                    @else bg-blue-50 border-blue-500
                    @endif">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="font-semibold text-sm text-gray-900">{{ $issue['title'] }}</p>
                            <p class="text-xs text-gray-600 mt-1">{{ $issue['message'] }}</p>
                        </div>
                        @if(isset($issue['count']))
                        <span class="ml-2 px-2 py-1 text-xs font-bold rounded-full 
                            @if($issue['type'] === 'error') bg-red-200 text-red-800
                            @elseif($issue['type'] === 'warning') bg-yellow-200 text-yellow-800
                            @else bg-blue-200 text-blue-800
                            @endif">
                            {{ $issue['count'] }}
                        </span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Monthly Summaries -->
        @if(count($termSummaries) > 0)
        <div class="px-4 mb-4">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">Monthly Summaries</h2>
            <div class="space-y-2">
                @foreach($termSummaries as $summary)
                <div class="bg-white rounded-lg p-3 border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <p class="font-semibold text-sm text-gray-900">{{ $summary['period'] ?? ($summary['month'] . ' ' . $summary['year']) }}</p>
                            <p class="text-xs text-gray-500">{{ $summary['academic_year'] }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded">
                            {{ $summary['total_payments'] }} payments
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 mt-2 pt-2 border-t border-gray-100">
                        <div>
                            <p class="text-xs text-gray-600">Total Collected</p>
                            <p class="text-sm font-bold text-gray-900">KES {{ number_format($summary['total_amount'], 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600">Discounts</p>
                            <p class="text-sm font-bold text-green-600">KES {{ number_format($summary['total_discounts'], 2) }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Recent Transactions -->
        <div class="px-4 mb-4">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-semibold text-gray-900">Recent Transactions</h2>
                <a href="{{ route('receipts.index') }}" class="text-sm text-blue-600 font-medium">View All</a>
            </div>
            <div class="space-y-2">
                @forelse($recentTransactions as $transaction)
                <div class="bg-white rounded-lg p-3 border border-gray-200 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="font-semibold text-sm text-gray-900">{{ $transaction->student->full_name }}</p>
                            <p class="text-xs text-gray-600">{{ $transaction->course->name }}</p>
                            @if($transaction->academic_year && $transaction->month)
                            <p class="text-xs text-gray-500 mt-1">{{ $transaction->month }} {{ $transaction->year ?? '' }} - {{ $transaction->academic_year }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-900">KES {{ number_format($transaction->amount_paid, 2) }}</p>
                            <p class="text-xs text-gray-500">{{ $transaction->created_at->format('M d') }}</p>
                        </div>
                    </div>
                    @if($transaction->receipt)
                    <div class="mt-2 pt-2 border-t border-gray-100">
                        <a href="{{ route('receipts.show', $transaction->receipt->id) }}" class="text-xs text-blue-600 font-medium">
                            View Receipt →
                        </a>
                    </div>
                    @endif
                </div>
                @empty
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <p class="text-sm text-gray-500">No recent transactions</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="px-4 pb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">Quick Actions</h2>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('billing.index') }}" class="bg-blue-600 text-white rounded-lg p-4 text-center font-semibold hover:bg-blue-700 transition-colors">
                    Process Payment
                </a>
                <a href="{{ route('reports.index') }}" class="bg-green-600 text-white rounded-lg p-4 text-center font-semibold hover:bg-green-700 transition-colors">
                    View Reports
                </a>
            </div>
        </div>
    </div>

    <script>
        function mobileDashboard() {
            return {
                init() {
                    // Refresh data every 30 seconds
                    setInterval(() => {
                        window.location.reload();
                    }, 30000);
                }
            }
        }
    </script>
</body>
</html>

