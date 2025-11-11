@extends('layouts.app')
@section('content')
<div class="container-fluid py-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-defult">
                    <h2 class="card-title">
                        <i class="fa-solid fa-eye mr-2"></i>
                        Transaction Details - {{ $Transaction->reference_number }}
                    </h2>
                    <div class="card-tools">
                        <a href="{{ route('transactions.edit', $Transaction->id) }}" class="btn bg-navy btn-sm">
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
                                    <td><strong>{{ $Transaction->reference_number }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Type:</th>
                                    <td>
                                        <span class="badge bg-{{ $Transaction->type == 'Income' ? 'success' : 'danger' }}">
                                            {{ $Transaction->type }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Category:</th>
                                    <td>{{ $Transaction->category }}</td>
                                </tr>
                                <tr>
                                    <th>Description:</th>
                                    <td>{{ $Transaction->description }}</td>
                                </tr>
                                <tr>
                                    <th>Amount:</th>
                                    <td>
                                        <strong class="text-{{ $Transaction->type == 'Income' ? 'success' : 'danger' }}">
                                            ${{ number_format($Transaction->amount, 2) }}
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
                                    <td>{{ $Transaction->transaction_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Payment Method:</th>
                                    <td>{{ $Transaction->payment_method }}</td>
                                </tr>
                                <tr>
                                    <th>Related Booking:</th>
                                    <td>
                                        @if($Transaction->booking)
                                            <a href="{{ route('bookings.show', $Transaction->booking_id) }}" class="text-primary">
                                                {{ $Transaction->booking->reference_number }}
                                            </a>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created At:</th>
                                    <td>{{ $Transaction->created_at->format('M d, Y h:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($Transaction->notes)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2">Notes</h5>
                            <div class="alert alert-info">
                                {{ $Transaction->notes }}
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($Transaction->booking)
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
                                            <strong>Guest:</strong> {{ $Transaction->booking->guest_name }}<br>
                                            <strong>Room:</strong> Room {{ $Transaction->booking->room->RoomNo }}<br>
                                            <strong>Check-in:</strong> {{ $Transaction->booking->check_in->format('M d, Y') }}
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Booking Reference:</strong> {{ $Transaction->booking->reference_number }}<br>
                                            <strong>Total Amount:</strong> ${{ number_format($Transaction->booking->total_amount, 2) }}<br>
                                            <strong>Status:</strong>
                                            <span class="badge bg-{{ $Transaction->booking->booking_status == 'Confirmed' ? 'primary' : ($Transaction->booking->booking_status == 'Checked In' ? 'success' : 'info') }}">
                                                {{ $Transaction->booking->booking_status }}
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
