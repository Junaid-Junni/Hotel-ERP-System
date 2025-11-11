<?php

namespace App\Http\Controllers;

use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Booking;
use Exception;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        $Transactions = Transaction::all();

        // Calculate totals
        $totalIncome = Transaction::income()->sum('amount');
        $totalExpense = Transaction::expense()->sum('amount');
        $netBalance = $totalIncome - $totalExpense;

        // Monthly totals
        $monthlyIncome = Transaction::income()->thisMonth()->sum('amount');
        $monthlyExpense = Transaction::expense()->thisMonth()->sum('amount');
        $monthlyNet = $monthlyIncome - $monthlyExpense;

        if (request()->ajax()) {
            return DataTables::of($this->dtQuery())
                ->addColumn('action', 'transactions.dt_buttons')
                ->make(true);
        }

        return view('transactions.index', compact(
            'Transactions',
            'totalIncome',
            'totalExpense',
            'netBalance',
            'monthlyIncome',
            'monthlyExpense',
            'monthlyNet'
        ));
    }

    public function dtQuery()
    {
        return Transaction::with('booking')->select('transactions.*');
    }

    public function create()
    {
        $bookings = Booking::where('payment_status', '!=', 'Paid')->get();
        $categories = [
            'Income' => [
                'Room Booking',
                'Food & Beverage',
                'Laundry Service',
                'Other Services',
                'Miscellaneous Income'
            ],
            'Expense' => [
                'Staff Salary',
                'Utilities',
                'Maintenance',
                'Supplies',
                'Marketing',
                'Insurance',
                'Taxes',
                'Other Expenses'
            ]
        ];

        return view('transactions.create', compact('bookings', 'categories'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'type' => 'required|in:Income,Expense',
                'category' => 'required|string|max:255',
                'description' => 'required|string|max:500',
                'amount' => 'required|numeric|min:0',
                'transaction_date' => 'required|date',
                'payment_method' => 'required|string',
                'booking_id' => 'nullable|exists:bookings,id',
                'notes' => 'nullable|string'
            ]);

            Transaction::create($request->all());

            DB::commit();

            return redirect()->route('transactions.index')
                ->with('success', 'Transaction added successfully!');
        } catch (Exception $error) {
            DB::rollBack();
            return back()->with('error', $error->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $Transaction = Transaction::with('booking')->find($id);
        return view('transactions.show', compact('Transaction'));
    }

    public function edit($id)
    {
        $Transaction = Transaction::find($id);
        $bookings = Booking::where('payment_status', '!=', 'Paid')->get();
        $categories = [
            'Income' => [
                'Room Booking',
                'Food & Beverage',
                'Laundry Service',
                'Other Services',
                'Miscellaneous Income'
            ],
            'Expense' => [
                'Staff Salary',
                'Utilities',
                'Maintenance',
                'Supplies',
                'Marketing',
                'Insurance',
                'Taxes',
                'Other Expenses'
            ]
        ];

        return view('transactions.edit', compact('Transaction', 'bookings', 'categories'));
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'type' => 'required|in:Income,Expense',
                'category' => 'required|string|max:255',
                'description' => 'required|string|max:500',
                'amount' => 'required|numeric|min:0',
                'transaction_date' => 'required|date',
                'payment_method' => 'required|string',
                'booking_id' => 'nullable|exists:bookings,id',
                'notes' => 'nullable|string'
            ]);

            $transaction = Transaction::find($id);
            $transaction->update($request->all());

            DB::commit();

            return redirect()->route('transactions.index')
                ->with('success', 'Transaction updated successfully!');
        } catch (Exception $error) {
            DB::rollBack();
            return back()->with('error', $error->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        Transaction::find($id)->delete();
        return back()->with('success', 'Transaction moved to trash successfully!');
    }

    public function trash()
    {
        $Transactions = Transaction::onlyTrashed()->with('booking')->get();
        return view('transactions.trash', compact('Transactions'));
    }

    public function restore($id)
    {
        $transaction = Transaction::withTrashed()->find($id);
        $transaction->restore();
        return back()->with('success', 'Transaction restored successfully!');
    }

    public function forceDelete($id)
    {
        $transaction = Transaction::withTrashed()->find($id);
        $transaction->forceDelete();
        return back()->with('success', 'Transaction permanently deleted!');
    }

    public function emptyTrash()
    {
        Transaction::onlyTrashed()->forceDelete();
        return back()->with('success', 'Trash emptied successfully!');
    }

    public function incomeReport()
    {
        $incomeByCategory = Transaction::income()
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();

        $monthlyIncome = Transaction::income()
            ->select(DB::raw('YEAR(transaction_date) as year, MONTH(transaction_date) as month, SUM(amount) as total'))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $totalIncome = $incomeByCategory->sum('total');

        return view('transactions.income-report', compact('incomeByCategory', 'monthlyIncome', 'totalIncome'));
    }

    public function expenseReport()
    {
        $expenseByCategory = Transaction::expense()
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();

        $monthlyExpense = Transaction::expense()
            ->select(DB::raw('YEAR(transaction_date) as year, MONTH(transaction_date) as month, SUM(amount) as total'))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $totalExpense = $expenseByCategory->sum('total');

        return view('transactions.expense-report', compact('expenseByCategory', 'monthlyExpense', 'totalExpense'));
    }

    public function financialSummary()
    {
        $currentMonthIncome = Transaction::income()->thisMonth()->sum('amount');
        $currentMonthExpense = Transaction::expense()->thisMonth()->sum('amount');
        $currentMonthNet = $currentMonthIncome - $currentMonthExpense;

        $totalIncome = Transaction::income()->sum('amount');
        $totalExpense = Transaction::expense()->sum('amount');
        $totalNet = $totalIncome - $totalExpense;

        $recentTransactions = Transaction::with('booking')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('transactions.financial-summary', compact(
            'currentMonthIncome',
            'currentMonthExpense',
            'currentMonthNet',
            'totalIncome',
            'totalExpense',
            'totalNet',
            'recentTransactions'
        ));
    }
}
