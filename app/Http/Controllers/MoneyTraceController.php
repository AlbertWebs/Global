<?php

namespace App\Http\Controllers;

use App\Models\BankDeposit;
use App\Models\LedgerEntry;
use Illuminate\Http\Request;

class MoneyTraceController extends Controller
{
    public function index(Request $request)
    {
        // Only Super Admin can access money trace
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can access Money Trace');
        }
        $dateFrom = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', now()->endOfDay()->toDateString());
        $period = $request->get('period', 'month');
        $holdingAccount = $request->get('holding_account');

        // Set default periods
        if ($period === 'today') {
            $dateFrom = now()->startOfDay()->toDateString();
            $dateTo = now()->endOfDay()->toDateString();
        } elseif ($period === 'week') {
            $dateFrom = now()->startOfWeek()->toDateString();
            $dateTo = now()->endOfWeek()->toDateString();
        } elseif ($period === 'month') {
            $dateFrom = now()->startOfMonth()->toDateString();
            $dateTo = now()->endOfMonth()->toDateString();
        }

        // Get current balances
        $balances = LedgerEntry::getAllBalances();
        $totalBalance = array_sum($balances);

        // Get income by source (today, week, month)
        $todayIncome = $this->getIncomeBySource(now()->startOfDay(), now()->endOfDay());
        $weekIncome = $this->getIncomeBySource(now()->startOfWeek(), now()->endOfWeek());
        $monthIncome = $this->getIncomeBySource(now()->startOfMonth(), now()->endOfMonth());

        // Get ledger entries with filters
        $query = LedgerEntry::with(['recorder', 'entity'])
            ->whereBetween('transaction_date', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->orderBy('transaction_date', 'desc');

        if ($holdingAccount) {
            $query->where('holding_account', $holdingAccount);
        }

        $entries = $query->paginate(50);

        // Calculate totals for filtered period
        // Exclude bank deposits from income calculations (they're transfers, not income)
        $periodTotals = [
            'inflows' => LedgerEntry::whereBetween('transaction_date', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->where('type', 'inflow')
                ->where(function($query) {
                    $query->where('entity_type', '!=', BankDeposit::class)
                          ->orWhereNull('entity_type');
                })
                ->when($holdingAccount, fn($q) => $q->where('holding_account', $holdingAccount))
                ->sum('amount'),
            'outflows' => LedgerEntry::whereBetween('transaction_date', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->where('type', 'outflow')
                ->where(function($query) {
                    $query->where('entity_type', '!=', BankDeposit::class)
                          ->orWhereNull('entity_type');
                })
                ->when($holdingAccount, fn($q) => $q->where('holding_account', $holdingAccount))
                ->sum('amount'),
        ];
        $periodTotals['net'] = $periodTotals['inflows'] - $periodTotals['outflows'];

        return view('money-trace.index', compact(
            'balances',
            'totalBalance',
            'todayIncome',
            'weekIncome',
            'monthIncome',
            'entries',
            'dateFrom',
            'dateTo',
            'period',
            'holdingAccount',
            'periodTotals'
        ));
    }

    private function getIncomeBySource($startDate, $endDate): array
    {
        // Exclude bank deposits - these are transfers, not income
        // Income only comes from actual payments (Payments), not transfers between accounts
        $inflows = LedgerEntry::where('type', 'inflow')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->where(function($query) {
                $query->where('entity_type', '!=', BankDeposit::class)
                      ->orWhereNull('entity_type');
            })
            ->selectRaw('payment_source, SUM(amount) as total')
            ->groupBy('payment_source')
            ->pluck('total', 'payment_source')
            ->toArray();

        return [
            'mpesa' => $inflows['mpesa'] ?? 0,
            'cash' => $inflows['cash'] ?? 0,
            'bank_transfer' => $inflows['bank_transfer'] ?? 0,
            'total' => array_sum($inflows),
        ];
    }
}
