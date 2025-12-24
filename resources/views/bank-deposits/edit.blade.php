@extends('layouts.app')

@section('title', 'Edit Bank Deposit')
@section('page-title', 'Edit Bank Deposit')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Edit Bank Deposit</h2>

        <form method="POST" action="{{ route('bank-deposits.update', $bankDeposit->id) }}">
            @csrf
            @method('PUT')

            <!-- Source Account -->
            <div class="mb-6">
                <label for="source_account" class="block text-sm font-medium text-gray-700 mb-2">Source Account *</label>
                <select 
                    id="source_account" 
                    name="source_account" 
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="cash_on_hand" {{ $bankDeposit->source_account === 'cash_on_hand' ? 'selected' : '' }}>Cash on Hand</option>
                    <option value="mpesa_wallet" {{ $bankDeposit->source_account === 'mpesa_wallet' ? 'selected' : '' }}>M-Pesa Wallet</option>
                </select>
                @error('source_account')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Amount -->
            <div class="mb-6">
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Amount (KES) *</label>
                <input 
                    type="number" 
                    id="amount" 
                    name="amount" 
                    step="0.01"
                    min="0.01"
                    value="{{ old('amount', $bankDeposit->amount) }}"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                @error('amount')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Deposit Date -->
            <div class="mb-6">
                <label for="deposit_date" class="block text-sm font-medium text-gray-700 mb-2">Deposit Date *</label>
                <input 
                    type="date" 
                    id="deposit_date" 
                    name="deposit_date" 
                    value="{{ old('deposit_date', $bankDeposit->deposit_date->toDateString()) }}"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                @error('deposit_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Reference Number -->
            <div class="mb-6">
                <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-2">Reference Number</label>
                <input 
                    type="text" 
                    id="reference_number" 
                    name="reference_number" 
                    value="{{ old('reference_number', $bankDeposit->reference_number) }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Bank deposit slip number, transaction ID, etc."
                >
                @error('reference_number')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea 
                    id="notes" 
                    name="notes" 
                    rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >{{ old('notes', $bankDeposit->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('bank-deposits.show', $bankDeposit->id) }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button 
                    type="submit" 
                    class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg hover:shadow-xl"
                >
                    Update Deposit
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

