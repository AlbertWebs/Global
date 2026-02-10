<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\OtherIncome;
use App\Models\OtherIncomeReceipt;
use Illuminate\Http\Request;

class OtherIncomeController extends Controller
{
    public function index(Request $request)
    {
        $query = OtherIncome::with('recorder');

        // Cashier can only see their own income records
        if (auth()->user()->isCashier()) {
            $query->where('recorded_by', auth()->id());
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('income_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('income_date', '<=', $request->end_date);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by search term (description)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $otherIncomes = $query->latest('income_date')->paginate(20);
        
        $totalIncome = $otherIncomes->sum('amount');
        $searchTerm = $request->get('search', '');
        $paymentMethodFilter = $request->get('payment_method', '');
        $startDate = $request->get('start_date', '');
        $endDate = $request->get('end_date', '');

        return view('other-incomes.index', compact('otherIncomes', 'totalIncome', 'searchTerm', 'paymentMethodFilter', 'startDate', 'endDate'));
    }

    public function create()
    {
        return view('other-incomes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:mpesa,cash,bank_transfer'],
            'income_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'generate_receipt' => ['nullable', 'boolean'],
        ]);

        $otherIncome = OtherIncome::create([
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'income_date' => $validated['income_date'],
            'notes' => $validated['notes'] ?? null,
            'recorded_by' => auth()->id(),
        ]);

        // Generate receipt if requested
        if ($request->has('generate_receipt') && $request->generate_receipt) {
            $receipt = OtherIncomeReceipt::create([
                'other_income_id' => $otherIncome->id,
                'receipt_number' => OtherIncomeReceipt::generateReceiptNumber(),
                'receipt_date' => now(),
            ]);
        }

        // Log the activity
        ActivityLog::log(
            'other_income.created',
            "Recorded other income: {$otherIncome->description} - KES " . number_format($otherIncome->amount, 2) . " ({$otherIncome->payment_method_label})",
            $otherIncome
        );

        if (isset($receipt)) {
            return redirect()->route('other-incomes.receipt', $receipt->id)
                ->with('success', 'Other income recorded and receipt generated successfully!');
        }

        return redirect()->route('other-incomes.index')
            ->with('success', 'Other income recorded successfully!');
    }

    public function show(OtherIncome $otherIncome)
    {
        // Cashier can only view their own income records
        if (auth()->user()->isCashier() && $otherIncome->recorded_by !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        $otherIncome->load('recorder', 'receipt');
        return view('other-incomes.show', compact('otherIncome'));
    }

    public function edit(OtherIncome $otherIncome)
    {
        // Cashier can only edit their own income records
        if (auth()->user()->isCashier() && $otherIncome->recorded_by !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        return view('other-incomes.edit', compact('otherIncome'));
    }

    public function update(Request $request, OtherIncome $otherIncome)
    {
        // Cashier can only update their own income records
        if (auth()->user()->isCashier() && $otherIncome->recorded_by !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:mpesa,cash,bank_transfer'],
            'income_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $oldAmount = $otherIncome->amount;
        $otherIncome->update($validated);

        // Log the activity
        ActivityLog::log(
            'other_income.updated',
            "Updated other income: {$otherIncome->description} - Amount changed from KES " . number_format($oldAmount, 2) . " to KES " . number_format($otherIncome->amount, 2),
            $otherIncome
        );

        return redirect()->route('other-incomes.index')
            ->with('success', 'Other income updated successfully!');
    }

    public function destroy(OtherIncome $otherIncome)
    {
        // Only Super Admin can delete income records
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $description = $otherIncome->description;
        $amount = $otherIncome->amount;
        $otherIncome->delete();

        // Log the activity
        ActivityLog::log(
            'other_income.deleted',
            "Deleted other income: {$description} - KES " . number_format($amount, 2),
            null
        );

        return redirect()->route('other-incomes.index')
            ->with('success', 'Other income deleted successfully!');
    }

    public function generateReceipt(OtherIncome $otherIncome)
    {
        // Check if receipt already exists
        if ($otherIncome->receipt) {
            return redirect()->route('other-incomes.receipt', $otherIncome->receipt->id)
                ->with('info', 'Receipt already exists for this income record.');
        }

        $receipt = OtherIncomeReceipt::create([
            'other_income_id' => $otherIncome->id,
            'receipt_number' => OtherIncomeReceipt::generateReceiptNumber(),
            'receipt_date' => now(),
        ]);

        return redirect()->route('other-incomes.receipt', $receipt->id)
            ->with('success', 'Receipt generated successfully!');
    }

    public function showReceipt(OtherIncomeReceipt $receipt)
    {
        $receipt->load('otherIncome.recorder');
        return view('other-incomes.receipt', compact('receipt'));
    }
}
