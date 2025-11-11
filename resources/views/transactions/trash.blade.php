@extends('layouts.app')
@section('content')
<div class="container-fluid py-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-defult">
                    <div class="card-title">
                        <h2 class="card-title">
                            <i class="fa-solid fa-recycle mr-2"></i>
                            Deleted Transactions
                        </h2>
                    </div>
                    <div class="card-tools">
                        <form action="{{ route('transactions.emptyTrash') }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm bg-maroon text-capitalize"
                                    onclick="return confirm('Are you sure you want to permanently delete ALL transactions? This action cannot be undone!')">
                                <i class="fa-solid fa-trash-can mr-2"></i>Empty Trash
                            </button>
                        </form>
                        <a href="{{ route('transactions.index') }}" class="btn btn-sm bg-navy text-capitalize ml-2">
                            <i class="fa-solid fa-arrow-left mr-2"></i>Back to Transactions
                        </a>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-responsive table-borderless">
                        <thead>
                            <tr class="border-bottom">
                                <th>Ref No</th>
                                <th>Type</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Deleted At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($Transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->reference_number }}</td>
                                <td>
                                    <span class="badge bg-{{ $transaction->type == 'Income' ? 'success' : 'danger' }}">
                                        {{ $transaction->type }}
                                    </span>
                                </td>
                                <td>{{ $transaction->category }}</td>
                                <td>{{ $transaction->description }}</td>
                                <td class="text-{{ $transaction->type == 'Income' ? 'success' : 'danger' }}">
                                    ${{ number_format($transaction->amount, 2) }}
                                </td>
                                <td>{{ $transaction->transaction_date->format('M d, Y') }}</td>
                                <td>{{ $transaction->deleted_at->format('M d, Y h:i A') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <form action="{{ route('transactions.restore', $transaction->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm"
                                                   onclick="return confirm('Restore this transaction?')">
                                                <i class="fa-solid fa-rotate-left"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('transactions.forceDelete', $transaction->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Permanently delete this transaction? This action cannot be undone!')">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fa-solid fa-trash-can fa-2x mb-3"></i><br>
                                    No deleted transactions found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
