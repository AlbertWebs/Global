@extends('layouts.app')

@section('title', 'Adjust Balance #' . $balance->id)
@section('page-title', 'Adjust Balance')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Adjust Balance for {{ $balance->student->full_name }} ({{ $balance->course->name }})</h2>

        <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <p class="text-sm text-gray-600">Current Outstanding Balance:</p>
            <p class="text-2xl font-bold text-blue-800">KES {{ number_format($balance->outstanding_balance, 2) }}</p>
        </div>

        <form method="POST" action="{{ route('balances.update', $balance->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label for="adjustment_amount" class="block text-sm font-medium text-gray-700 mb-2">Adjustment Amount (KES) *</label>
                <input 
                    type="number" 
                    id="adjustment_amount" 
                    name="adjustment_amount" 
                    step="0.01"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg font-semibold"
                    placeholder="0.00"
                >
                <p class="mt-2 text-sm text-gray-500">Enter a positive value to increase balance, negative to decrease (e.g., -5000 for a 5000 KES reduction).</p>
            </div>

            <div class="mb-6">
                <label for="adjustment_reason" class="block text-sm font-medium text-gray-700 mb-2">Reason for Adjustment *</label>
                <textarea 
                    id="adjustment_reason" 
                    name="adjustment_reason" 
                    rows="3"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Reason for this manual adjustment..."></textarea>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('balances.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors shadow-md">
                    Apply Adjustment
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

