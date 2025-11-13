<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Employee;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['employee', 'booking'])
            ->latest()
            ->paginate(10);

        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'Active')->get();
        $bookings = Booking::whereIn('status', ['Confirmed', 'Checked In'])->get();

        return view('transactions.create', compact('employees', 'bookings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string',
            'payment_method' => 'required|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'employee_id' => 'nullable|exists:employees,id',
            'booking_id' => 'nullable|exists:bookings,id',
            'status' => 'required|in:pending,completed,cancelled'
        ]);

        try {
            DB::beginTransaction();

            $transaction = Transaction::create($validated);

            // If linked to booking and it's income, update booking paid amount
            if ($transaction->booking_id && $transaction->type == 'income' && $transaction->status == 'completed') {
                $booking = Booking::find($transaction->booking_id);
                $booking->increment('paid_amount', $transaction->amount);

                // Update payment status based on paid amount
                if ($booking->paid_amount >= $booking->total_amount) {
                    $booking->update(['payment_status' => 'Paid']);
                } elseif ($booking->paid_amount > 0) {
                    $booking->update(['payment_status' => 'Partial']);
                }
            }

            DB::commit();

            return redirect()->route('transactions.index')
                ->with('success', 'Transaction created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create transaction: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $transaction = Transaction::with(['employee', 'booking.room'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'transaction' => $transaction
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found.'
            ], 404);
        }
    }

    public function edit(Transaction $transaction)
    {
        $employees = Employee::where('status', 'Active')->get();
        $bookings = Booking::whereIn('status', ['Confirmed', 'Checked In'])->get();

        return view('transactions.edit', compact('transaction', 'employees', 'bookings'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string',
            'payment_method' => 'required|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'employee_id' => 'nullable|exists:employees,id',
            'booking_id' => 'nullable|exists:bookings,id',
            'status' => 'required|in:pending,completed,cancelled'
        ]);

        try {
            DB::beginTransaction();

            // Handle booking payment adjustments if amount or type changed
            if ($transaction->booking_id && $transaction->type == 'income' && $transaction->status == 'completed') {
                $oldAmount = $transaction->amount;
                $booking = Booking::find($transaction->booking_id);
                $booking->decrement('paid_amount', $oldAmount);
            }

            $transaction->update($validated);

            // Update booking paid amount for new transaction
            if ($transaction->booking_id && $transaction->type == 'income' && $transaction->status == 'completed') {
                $booking = Booking::find($transaction->booking_id);
                $booking->increment('paid_amount', $transaction->amount);

                if ($booking->paid_amount >= $booking->total_amount) {
                    $booking->update(['payment_status' => 'Paid']);
                } elseif ($booking->paid_amount > 0) {
                    $booking->update(['payment_status' => 'Partial']);
                } else {
                    $booking->update(['payment_status' => 'Pending']);
                }
            }

            DB::commit();

            return redirect()->route('transactions.index')
                ->with('success', 'Transaction updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update transaction: ' . $e->getMessage());
        }
    }

    public function destroy(Transaction $transaction)
    {
        try {
            DB::beginTransaction();

            // Reverse booking payment if this was an income transaction
            if ($transaction->booking_id && $transaction->type == 'income' && $transaction->status == 'completed') {
                $booking = Booking::find($transaction->booking_id);
                $booking->decrement('paid_amount', $transaction->amount);

                // Recalculate payment status
                if ($booking->paid_amount <= 0) {
                    $booking->update(['payment_status' => 'Pending']);
                } elseif ($booking->paid_amount < $booking->total_amount) {
                    $booking->update(['payment_status' => 'Partial']);
                }
            }

            $transaction->delete();

            DB::commit();

            return redirect()->route('transactions.index')
                ->with('success', 'Transaction moved to trash successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete transaction: ' . $e->getMessage());
        }
    }

    public function trash()
    {
        $trashedTransactions = Transaction::onlyTrashed()
            ->with(['employee', 'booking'])
            ->latest()
            ->get();

        return view('transactions.trash', compact('trashedTransactions'));
    }

    public function restore($id)
    {
        try {
            DB::beginTransaction();

            $transaction = Transaction::onlyTrashed()->findOrFail($id);
            $transaction->restore();

            // Restore booking payment if this was an income transaction
            if ($transaction->booking_id && $transaction->type == 'income' && $transaction->status == 'completed') {
                $booking = Booking::find($transaction->booking_id);
                $booking->increment('paid_amount', $transaction->amount);

                if ($booking->paid_amount >= $booking->total_amount) {
                    $booking->update(['payment_status' => 'Paid']);
                } elseif ($booking->paid_amount > 0) {
                    $booking->update(['payment_status' => 'Partial']);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction restored successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    public function forceDelete($id)
    {
        try {
            $transaction = Transaction::onlyTrashed()->findOrFail($id);
            $transaction->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Transaction permanently deleted.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    public function emptyTrash()
    {
        try {
            $trashedCount = Transaction::onlyTrashed()->count();
            Transaction::onlyTrashed()->forceDelete();

            return response()->json([
                'success' => true,
                'message' => "Successfully emptied trash. {$trashedCount} transactions permanently deleted."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to empty trash: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reports()
    {
        return view('transactions.reports');
    }

    public function getReportData(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'nullable|in:income,expense'
        ]);

        $query = Transaction::completed()
            ->dateRange($request->start_date, $request->end_date);

        if ($request->type) {
            $query->where('type', $request->type);
        }

        $transactions = $query->with(['employee', 'booking'])
            ->get();

        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $netProfit = $totalIncome - $totalExpense;

        // Category-wise breakdown
        $incomeByCategory = $transactions->where('type', 'income')
            ->groupBy('category')
            ->map(function ($items) {
                return $items->sum('amount');
            });

        $expenseByCategory = $transactions->where('type', 'expense')
            ->groupBy('category')
            ->map(function ($items) {
                return $items->sum('amount');
            });

        return response()->json([
            'transactions' => $transactions,
            'summary' => [
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
                'net_profit' => $netProfit,
                'transaction_count' => $transactions->count()
            ],
            'income_by_category' => $incomeByCategory,
            'expense_by_category' => $expenseByCategory
        ]);
    }
}
