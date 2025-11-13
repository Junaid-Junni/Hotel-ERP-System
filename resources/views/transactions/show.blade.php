@extends('layouts.app')
@section('content')
<div class="container-fluid py-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-defult">
                    <h2 class="card-title">
                        <i class="fa-solid fa-eye mr-2"></i>
                        {{-- Transaction Details - {{ $transaction->reference_number }} --}}
                    </h2>
                    <div class="card-tools">
                        <a href="{{ route('transactions.edit', $transaction->id) }}" class="btn bg-navy btn-sm">
                            <i class="fa-solid fa-edit mr-1"></i>Edit
                        </a>
                        <a href="{{ route('transactions.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fa-solid fa-arrow-left mr-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Transaction Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Reference Number:</th>
                                    <td><strong>{{ $transaction->reference_number }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Type:</th>
                                    <td>
                                        <span class="badge bg-{{ $transaction->type == 'Income' ? 'success' : 'danger' }}">
                                            {{ $transaction->type }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Category:</th>
                                    <td>{{ $transaction->category }}</td>
                                </tr>
                                <tr>
                                    <th>Description:</th>
                                    <td>{{ $transaction->description }}</td>
                                </tr>
                                <tr>
                                    <th>Amount:</th>
                                    <td>
                                        <strong class="text-{{ $transaction->type == 'Income' ? 'success' : 'danger' }}">
                                            ${{ number_format($transaction->amount, 2) }}
                                        </strong>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Additional Details</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Transaction Date:</th>
                                    <td>{{ $transaction->transaction_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Payment Method:</th>
                                    <td>{{ $transaction->payment_method }}</td>
                                </tr>
                                <tr>
                                    <th>Related Booking:</th>
                                    <td>
                                        @if($transaction->booking)
                                            <a href="{{ route('bookings.show', $transaction->booking_id) }}" class="text-primary">
                                                {{ $transaction->booking->reference_number }}
                                            </a>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created At:</th>
                                    <td>{{ $transaction->created_at->format('M d, Y h:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($transaction->notes)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2">Notes</h5>
                            <div class="alert alert-info">
                                {{ $transaction->notes }}
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($transaction->booking)
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fa-solid fa-calendar-alt mr-2"></i>
                                        Booking Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Guest:</strong> {{ $transaction->booking->guest_name }}<br>
                                            <strong>Room:</strong> Room {{ $transaction->booking->room->RoomNo }}<br>
                                            <strong>Check-in:</strong> {{ $transaction->booking->check_in->format('M d, Y') }}
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Booking Reference:</strong> {{ $transaction->booking->reference_number }}<br>
                                            <strong>Total Amount:</strong> ${{ number_format($transaction->booking->total_amount, 2) }}<br>
                                            <strong>Status:</strong>
                                            <span class="badge bg-{{ $transaction->booking->booking_status == 'Confirmed' ? 'primary' : ($transaction->booking->booking_status == 'Checked In' ? 'success' : 'info') }}">
                                                {{ $transaction->booking->booking_status }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
