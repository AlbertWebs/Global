@extends('layouts.app')

@section('title', 'Outstanding Balances')
@section('page-title', 'Outstanding Balances')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Manage Student Balances</h2>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('balances.index') }}" class="mb-6 p-4 bg-gray-50 rounded-lg shadow-sm grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">Filter by Student</label>
                <select id="student_id" name="student_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Students</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                            {{ $student->full_name }} ({{ $student->student_number }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">Filter by Course</label>
                <select id="course_id" name="course_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->name }} ({{ $course->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Filter by Status</label>
                <select id="status" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="partially_paid" {{ request('status') == 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                    <option value="cleared" {{ request('status') == 'cleared' ? 'selected' : '' }}>Cleared</option>
                </select>
            </div>
            <div class="md:col-span-3 flex justify-end space-x-3">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors shadow-md">
                    Apply Filters
                </button>
                <a href="{{ route('balances.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Clear Filters
                </a>
            </div>
        </form>

        <!-- Balances Table -->
        @if($balances->count() > 0)
        <div class="overflow-x-auto border border-gray-200 rounded-lg shadow-md">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Agreed Amount</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Paid</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Outstanding Balance</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Payment</th>
                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($balances as $balance)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $balance->student->full_name }}</div>
                            <div class="text-sm text-gray-500">{{ $balance->student->student_number }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $balance->course->name }}</div>
                            <div class="text-sm text-gray-500">{{ $balance->course->code }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">KES {{ number_format($balance->agreed_amount, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">KES {{ number_format($balance->total_paid, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-red-600">- KES {{ number_format($balance->discount_amount, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-lg font-bold {{ $balance->outstanding_balance > 0 ? 'text-orange-600' : 'text-green-600' }}">KES {{ number_format($balance->outstanding_balance, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($balance->status === 'cleared') bg-green-100 text-green-800
                                @elseif($balance->status === 'partially_paid') bg-orange-100 text-orange-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $balance->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $balance->last_payment_date ? $balance->last_payment_date->format('M d, Y') : 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('balances.edit', $balance->id) }}" class="text-blue-600 hover:text-blue-900 mr-4">Edit</a>
                            <a href="{{ route('balances.show', $balance->id) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $balances->links() }}
        </div>
        @else
        <div class="p-4 bg-blue-50 border-l-4 border-blue-400 text-blue-700 rounded-lg">
            <p class="font-bold">No Balances Found</p>
            <p class="text-sm">There are no outstanding balances matching your criteria.</p>
        </div>
        @endif

    </div>
</div>
@endsection

