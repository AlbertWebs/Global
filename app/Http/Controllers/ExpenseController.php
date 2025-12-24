<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Expense;
use App\Models\LedgerEntry;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{

    public function index(Request $request)
    {
        $query = Expense::with('recorder');

        // Cashier can only see their own expenses
        if (auth()->user()->isCashier()) {
            $query->where('recorded_by', auth()->id());
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('expense_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('expense_date', '<=', $request->end_date);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by search term (title/description)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $expenses = $query->latest('expense_date')->paginate(20);
        
        $totalExpenses = $expenses->sum('amount');
        $searchTerm = $request->get('search', '');
        $paymentMethodFilter = $request->get('payment_method', '');
        $startDate = $request->get('start_date', '');
        $endDate = $request->get('end_date', '');

        return view('expenses.index', compact('expenses', 'totalExpenses', 'searchTerm', 'paymentMethodFilter', 'startDate', 'endDate'));
    }

    public function create()
    {
        return view('expenses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:mpesa,cash,bank_transfer'],
            'expense_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $expense = Expense::create([
            ...$validated,
            'recorded_by' => auth()->id(),
        ]);

        // Create ledger entry for money trace
        LedgerEntry::createFromExpense($expense);

        // Log the activity
        ActivityLog::log(
            'expense.created',
            "Recorded expense: {$expense->title} - KES " . number_format($expense->amount, 2) . " ({$expense->payment_method_label})",
            $expense
        );

        return redirect()->route('expenses.index')
            ->with('success', 'Expense recorded successfully!');
    }

    public function show(Expense $expense)
    {
        // Cashier can only view their own expenses
        if (auth()->user()->isCashier() && $expense->recorded_by !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        $expense->load('recorder');
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        // Cashier can only edit their own expenses
        if (auth()->user()->isCashier() && $expense->recorded_by !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        return view('expenses.edit', compact('expense'));
    }

    public function update(Request $request, Expense $expense)
    {
        // Cashier can only update their own expenses
        if (auth()->user()->isCashier() && $expense->recorded_by !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:mpesa,cash,bank_transfer'],
            'expense_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $oldAmount = $expense->amount;
        $expense->update($validated);

        // Log the activity
        ActivityLog::log(
            'expense.updated',
            "Updated expense: {$expense->title} - Amount changed from KES " . number_format($oldAmount, 2) . " to KES " . number_format($expense->amount, 2),
            $expense
        );

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully!');
    }

    public function destroy(Expense $expense)
    {
        // Only Super Admin can delete expenses
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $expenseTitle = $expense->title;
        $expenseAmount = $expense->amount;
        $expense->delete();

        // Log the activity
        ActivityLog::log(
            'expense.deleted',
            "Deleted expense: {$expenseTitle} - KES " . number_format($expenseAmount, 2),
            null
        );

        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully!');
    }
}
