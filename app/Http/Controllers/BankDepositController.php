<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\BankDeposit;
use App\Models\LedgerEntry;
use Illuminate\Http\Request;

class BankDepositController extends Controller
{
    public function index(Request $request)
    {
        // Only Super Admin can view all deposits, Cashier can view their own
        $query = BankDeposit::with('recorder');

        if (auth()->user()->isCashier()) {
            $query->where('recorded_by', auth()->id());
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('deposit_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('deposit_date', '<=', $request->end_date);
        }

        // Filter by source account
        if ($request->filled('source_account')) {
            $query->where('source_account', $request->source_account);
        }

        $deposits = $query->latest('deposit_date')->paginate(20);
        
        $totalDeposits = $deposits->sum('amount');
        $startDate = $request->get('start_date', '');
        $endDate = $request->get('end_date', '');
        $sourceAccountFilter = $request->get('source_account', '');

        return view('bank-deposits.index', compact('deposits', 'totalDeposits', 'startDate', 'endDate', 'sourceAccountFilter'));
    }

    public function create()
    {
        return view('bank-deposits.create');
    }

    public function getBalance(Request $request)
    {
        $request->validate([
            'source_account' => ['required', 'in:cash_on_hand,mpesa_wallet'],
        ]);

        $balance = LedgerEntry::getBalance($request->source_account);

        return response()->json([
            'balance' => $balance,
            'formatted_balance' => number_format($balance, 2),
            'has_balance' => $balance > 0,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'source_account' => ['required', 'in:cash_on_hand,mpesa_wallet'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'deposit_date' => ['required', 'date'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $deposit = BankDeposit::create([
            ...$validated,
            'recorded_by' => auth()->id(),
        ]);

        // Create ledger entries for the transfer
        // Note: This is a transfer between accounts, NOT new income
        // The income was already recorded when the original payment was received
        
        // Outflow from source account (cash on hand or M-Pesa wallet)
        LedgerEntry::create([
            'type' => 'outflow',
            'payment_source' => $deposit->source_account === 'cash_on_hand' ? 'cash' : 'mpesa',
            'holding_account' => $deposit->source_account,
            'amount' => $deposit->amount,
            'reference_number' => $deposit->reference_number,
            'description' => "Transfer to bank: {$deposit->source_account_label} → Bank Account",
            'entity_type' => BankDeposit::class,
            'entity_id' => $deposit->id,
            'recorded_by' => auth()->id(),
            'transaction_date' => $deposit->deposit_date,
        ]);

        // Inflow to bank account (transfer, not income)
        LedgerEntry::create([
            'type' => 'inflow',
            'payment_source' => 'bank_transfer',
            'holding_account' => 'bank_account',
            'amount' => $deposit->amount,
            'reference_number' => $deposit->reference_number,
            'description' => "Transfer from {$deposit->source_account_label} to Bank Account",
            'entity_type' => BankDeposit::class,
            'entity_id' => $deposit->id,
            'recorded_by' => auth()->id(),
            'transaction_date' => $deposit->deposit_date,
        ]);

        // Log the activity
        ActivityLog::log(
            'bank_deposit.created',
            "Recorded bank deposit: KES " . number_format($deposit->amount, 2) . " from {$deposit->source_account_label} to Bank Account" . ($deposit->reference_number ? " (Ref: {$deposit->reference_number})" : ''),
            $deposit
        );

        return redirect()->route('bank-deposits.index')
            ->with('success', 'Bank deposit recorded successfully!');
    }

    public function show(BankDeposit $bankDeposit)
    {
        // Cashier can only view their own deposits
        if (auth()->user()->isCashier() && $bankDeposit->recorded_by !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        $bankDeposit->load('recorder');
        return view('bank-deposits.show', compact('bankDeposit'));
    }

    public function edit(BankDeposit $bankDeposit)
    {
        // Only Super Admin can edit deposits
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access');
        }

        return view('bank-deposits.edit', compact('bankDeposit'));
    }

    public function update(Request $request, BankDeposit $bankDeposit)
    {
        // Only Super Admin can update deposits
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'source_account' => ['required', 'in:cash_on_hand,mpesa_wallet'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'deposit_date' => ['required', 'date'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $oldAmount = $bankDeposit->amount;
        $bankDeposit->update($validated);

        // Update ledger entries if amount changed
        if ($oldAmount != $bankDeposit->amount) {
            // Delete old ledger entries
            LedgerEntry::where('entity_type', BankDeposit::class)
                ->where('entity_id', $bankDeposit->id)
                ->delete();

            // Create new ledger entries (transfer, not income)
            LedgerEntry::create([
                'type' => 'outflow',
                'payment_source' => $bankDeposit->source_account === 'cash_on_hand' ? 'cash' : 'mpesa',
                'holding_account' => $bankDeposit->source_account,
                'amount' => $bankDeposit->amount,
                'reference_number' => $bankDeposit->reference_number,
                'description' => "Transfer to bank: {$bankDeposit->source_account_label} → Bank Account",
                'entity_type' => BankDeposit::class,
                'entity_id' => $bankDeposit->id,
                'recorded_by' => auth()->id(),
                'transaction_date' => $bankDeposit->deposit_date,
            ]);

            LedgerEntry::create([
                'type' => 'inflow',
                'payment_source' => 'bank_transfer',
                'holding_account' => 'bank_account',
                'amount' => $bankDeposit->amount,
                'reference_number' => $bankDeposit->reference_number,
                'description' => "Transfer from {$bankDeposit->source_account_label} to Bank Account",
                'entity_type' => BankDeposit::class,
                'entity_id' => $bankDeposit->id,
                'recorded_by' => auth()->id(),
                'transaction_date' => $bankDeposit->deposit_date,
            ]);
        }

        // Log the activity
        ActivityLog::log(
            'bank_deposit.updated',
            "Updated bank deposit: KES " . number_format($bankDeposit->amount, 2) . " from {$bankDeposit->source_account_label}",
            $bankDeposit
        );

        return redirect()->route('bank-deposits.index')
            ->with('success', 'Bank deposit updated successfully!');
    }

    public function destroy(BankDeposit $bankDeposit)
    {
        // Only Super Admin can delete deposits
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $amount = $bankDeposit->amount;
        $sourceAccount = $bankDeposit->source_account_label;

        // Delete associated ledger entries
        LedgerEntry::where('entity_type', BankDeposit::class)
            ->where('entity_id', $bankDeposit->id)
            ->delete();

        $bankDeposit->delete();

        // Log the activity
        ActivityLog::log(
            'bank_deposit.deleted',
            "Deleted bank deposit: KES " . number_format($amount, 2) . " from {$sourceAccount}",
            null
        );

        return redirect()->route('bank-deposits.index')
            ->with('success', 'Bank deposit deleted successfully!');
    }
}
