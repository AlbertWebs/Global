@extends('layouts.app')

@section('title', 'Record Bank Deposit')
@section('page-title', 'Record Bank Deposit')

@section('content')
<div class="max-w-2xl mx-auto" x-data="bankDepositForm()">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Record Bank Deposit</h2>

        <form method="POST" action="{{ route('bank-deposits.store') }}">
            @csrf

            <!-- Source Account -->
            <div class="mb-6">
                <label for="source_account" class="block text-sm font-medium text-gray-700 mb-2">Source Account *</label>
                <select 
                    id="source_account" 
                    name="source_account" 
                    x-model="sourceAccount"
                    @change="fetchBalance()"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="">Select source account...</option>
                    <option value="cash_on_hand">Cash on Hand</option>
                    <option value="mpesa_wallet">M-Pesa Wallet</option>
                </select>
                <p class="mt-1 text-sm text-gray-500">Select where the money is coming from</p>
                @error('source_account')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Loading Indicator -->
            <div x-show="loading" x-cloak class="mb-4">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="animate-spin h-5 w-5 text-blue-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="text-sm text-gray-600">Fetching balance...</p>
                    </div>
                </div>
            </div>

            <!-- Balance Message -->
            <div x-show="balanceMessage && !loading" x-cloak class="mb-4">
                <div 
                    x-bind:class="hasBalance ? 'bg-blue-50 border-blue-200 text-blue-800' : 'bg-yellow-50 border-yellow-200 text-yellow-800'"
                    class="border rounded-lg p-4"
                >
                    <div class="flex items-center">
                        <svg x-show="hasBalance" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <svg x-show="!hasBalance" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <p class="text-sm font-medium" x-text="balanceMessage"></p>
                    </div>
                </div>
            </div>

            <!-- Amount -->
            <div class="mb-6">
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Amount (KES) *</label>
                <input 
                    type="number" 
                    id="amount" 
                    name="amount" 
                    x-model="amount"
                    step="0.01"
                    min="0.01"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="0.00"
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
                    value="{{ old('deposit_date', now()->toDateString()) }}"
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
                    value="{{ old('reference_number') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Bank deposit slip number, transaction ID, etc."
                >
                <p class="mt-1 text-sm text-gray-500">Optional: Bank deposit slip number or transaction reference</p>
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
                    placeholder="Additional notes about this deposit..."
                >{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('bank-deposits.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button 
                    type="submit" 
                    class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg hover:shadow-xl"
                >
                    Record Deposit
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function bankDepositForm() {
    return {
        sourceAccount: '',
        amount: '',
        balanceMessage: '',
        hasBalance: false,
        loading: false,

        async fetchBalance() {
            if (!this.sourceAccount) {
                this.balanceMessage = '';
                this.amount = '';
                return;
            }

            this.loading = true;
            this.balanceMessage = '';

            try {
                const response = await fetch(`{{ route('bank-deposits.get-balance') }}?source_account=${this.sourceAccount}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });

                const data = await response.json();

                if (data.has_balance) {
                    this.amount = data.balance;
                    this.hasBalance = true;
                    this.balanceMessage = `Available balance: KES ${data.formatted_balance}`;
                } else {
                    this.amount = '';
                    this.hasBalance = false;
                    this.balanceMessage = 'No balance available in this account.';
                }
            } catch (error) {
                console.error('Error fetching balance:', error);
                this.balanceMessage = 'Unable to fetch balance. Please check manually.';
                this.hasBalance = false;
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection

