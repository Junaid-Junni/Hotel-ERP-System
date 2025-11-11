@extends('layouts.app')
@section('content')
<div class="container-fluid py-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-defult">
                    <div class="card-title">
                        <h2 class="card-title">
                            <a href="{{ route('transactions.create') }}" class="btn bg-navy text-capitalize mr-3">
                                <i class="fa-solid fa-plus-circle mr-2"></i>
                                Add Transaction
                            </a>
                            Transaction List
                        </h2>
                    </div>
                    <div class="card-tools">
                        <a class="btn btn-sm bg-success text-capitalize mr-2" href="{{ route('transactions.incomeReport') }}">
                            <i class="fa-solid fa-chart-line mr-2"></i>Income Report
                        </a>
                        <a class="btn btn-sm bg-danger text-capitalize mr-2" href="{{ route('transactions.expenseReport') }}">
                            <i class="fa-solid fa-chart-column mr-2"></i>Expense Report
                        </a>
                        <a class="btn btn-sm bg-navy text-capitalize" href="{{ route('transactions.trash') }}">
                            <i class="fa-solid fa-recycle mr-2"></i>View Trash
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-success text-white h-100">
                                <div class="card-body">
                                    <h6 class="card-title">Total Income</h6>
                                    <h3>${{ number_format($totalIncome, 2) }}</h3>
                                    <small>This Month: ${{ number_format($monthlyIncome, 2) }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-danger text-white h-100">
                                <div class="card-body">
                                    <h6 class="card-title">Total Expense</h6>
                                    <h3>${{ number_format($totalExpense, 2) }}</h3>
                                    <small>This Month: ${{ number_format($monthlyExpense, 2) }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-info text-white h-100">
                                <div class="card-body">
                                    <h6 class="card-title">Net Balance</h6>
                                    <h3>${{ number_format($netBalance, 2) }}</h3>
                                    <small>This Month: ${{ number_format($monthlyNet, 2) }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-warning text-dark h-100">
                                <div class="card-body">
                                    <h6 class="card-title">Transactions</h6>
                                    <h3>{{ $Transactions->count() }}</h3>
                                    <small>All Time Records</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Responsive Table Container -->
                    <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                        <table class="table table-hover table-bordered table-striped" id="TransactionList" style="min-width: 1200px; width: 100%;">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-nowrap">Ref No</th>
                                    <th class="text-nowrap">Type</th>
                                    <th class="text-nowrap">Category</th>
                                    <th class="text-nowrap">Description</th>
                                    <th class="text-nowrap">Amount</th>
                                    <th class="text-nowrap">Date</th>
                                    <th class="text-nowrap">Payment Method</th>
                                    <th class="text-nowrap">Booking</th>
                                    <th class="text-nowrap">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTable will populate this -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Responsive Alternative (Optional) -->
                    <div class="d-md-none">
                        <div class="alert alert-info">
                            <i class="fa-solid fa-mobile-screen mr-2"></i>
                            Scroll horizontally to view all columns
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom scrollbar styling */
    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Ensure table headers stay visible on scroll */
    .table thead th {
        position: sticky;
        top: 0;
        background: #343a40;
        color: white;
        z-index: 10;
    }

    /* Responsive table cell styling */
    @media (max-width: 768px) {
        .table-responsive {
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
        }

        .card-tools .btn {
            margin-bottom: 5px;
        }
    }

    /* Hover effects for better UX */
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.075);
    }

    /* Fixed column widths for better scrolling */
    #TransactionList th:nth-child(1), /* Ref No */
    #TransactionList td:nth-child(1) {
        min-width: 120px;
        max-width: 150px;
    }

    #TransactionList th:nth-child(2), /* Type */
    #TransactionList td:nth-child(2) {
        min-width: 80px;
        max-width: 100px;
    }

    #TransactionList th:nth-child(3), /* Category */
    #TransactionList td:nth-child(3) {
        min-width: 120px;
        max-width: 150px;
    }

    #TransactionList th:nth-child(4), /* Description */
    #TransactionList td:nth-child(4) {
        min-width: 200px;
        max-width: 300px;
    }

    #TransactionList th:nth-child(5), /* Amount */
    #TransactionList td:nth-child(5) {
        min-width: 100px;
        max-width: 120px;
    }

    #TransactionList th:nth-child(6), /* Date */
    #TransactionList td:nth-child(6) {
        min-width: 100px;
        max-width: 120px;
    }

    #TransactionList th:nth-child(7), /* Payment Method */
    #TransactionList td:nth-child(7) {
        min-width: 120px;
        max-width: 150px;
    }

    #TransactionList th:nth-child(8), /* Booking */
    #TransactionList td:nth-child(8) {
        min-width: 120px;
        max-width: 150px;
    }

    #TransactionList th:nth-child(9), /* Action */
    #TransactionList td:nth-child(9) {
        min-width: 100px;
        max-width: 120px;
    }
</style>

<script src="{{ asset('js/custom-js/transaction.js') }}"></script>

<script>
    // Additional responsive enhancements
    document.addEventListener('DOMContentLoaded', function() {
        // Add touch scrolling for mobile devices
        const tableContainer = document.querySelector('.table-responsive');

        if (tableContainer) {
            tableContainer.addEventListener('touchstart', function() {
                this.style.cursor = 'grabbing';
            });

            tableContainer.addEventListener('touchend', function() {
                this.style.cursor = 'grab';
            });
        }

        // Auto-adjust table width on window resize
        window.addEventListener('resize', function() {
            const table = document.getElementById('TransactionList');
            if (table) {
                table.style.width = '100%';
            }
        });
    });
</script>
@endsection
