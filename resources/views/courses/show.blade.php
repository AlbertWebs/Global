@extends('layouts.app')

@section('title', $course->name)
@section('page-title', 'Course Details')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Course Information</h2>
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Course Code</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $course->code }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Course Name</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $course->name }}</p>
                </div>
                @if($user->isSuperAdmin())
                <div>
                    <p class="text-sm text-gray-600 mb-1">Base Price</p>
                    <p class="text-2xl font-bold text-blue-600">KES {{ number_format($course->base_price, 2) }}</p>
                </div>
                @endif
                <div>
                    <p class="text-sm text-gray-600 mb-1">Status</p>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $course->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($course->status) }}
                    </span>
                </div>
                @if($course->description)
                <div>
                    <p class="text-sm text-gray-600 mb-1">Description</p>
                    <p class="text-gray-900">{{ $course->description }}</p>
                </div>
                @endif
            </div>
            @if(auth()->user()->isSuperAdmin())
            <div class="mt-6 flex space-x-4">
                <a href="{{ route('courses.edit', $course->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Edit</a>
                <a href="{{ route('courses.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Back</a>
            </div>
            @endif
        </div>

        @if($course->payments->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Payment History</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount Paid</th>
                            @if(auth()->user()->isSuperAdmin())
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Discount</th>
                            @endif
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($course->payments as $payment)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $payment->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $payment->student->full_name }}</td>
                            <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">KES {{ number_format($payment->amount_paid, 2) }}</td>
                            @if(auth()->user()->isSuperAdmin())
                            <td class="px-4 py-3 text-sm text-right text-green-600">KES {{ number_format($payment->discount_amount, 2) }}</td>
                            @endif
                            <td class="px-4 py-3 text-right">
                                @if($payment->receipt)
                                <a href="{{ route('receipts.show', $payment->receipt->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                @else
                                <span class="text-gray-400">N/A</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

