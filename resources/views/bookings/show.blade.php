@extends('layouts.app')
@section('content')
<div class="container-fluid py-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-defult">
                    <h2 class="card-title">
                        <i class="fa-solid fa-receipt mr-2"></i>
                        Booking Details - {{ $Booking->reference_number }}
                    </h2>
                    <div class="card-tools">
                        @if($Booking->booking_status == 'Confirmed')
                            <a href="{{ route('bookings.checkIn', $Booking->id) }}" class="btn btn-success btn-sm">
                                <i class="fa-solid fa-sign-in-alt mr-1"></i>Check In
                            </a>
                        @endif
                        @if($Booking->booking_status == 'Checked In')
                            <a href="{{ route('bookings.checkOut', $Booking->id) }}" class="btn btn-info btn-sm">
                                <i class="fa-solid fa-sign-out-alt mr-1"></i>Check Out
                            </a>
                        @endif
                        @if(in_array($Booking->booking_status, ['Confirmed', 'Checked In']))
                            <a href="{{ route('bookings.cancel', $Booking->id) }}" class="btn btn-danger btn-sm"
                               onclick="return confirm('Are you sure you want to cancel this booking?')">
                                <i class="fa-solid fa-times mr-1"></i>Cancel
                            </a>
                        @endif
                        <a href="{{ route('bookings.edit', $Booking->id) }}" class="btn bg-navy btn-sm">
                            <i class="fa-solid fa-edit mr-1"></i>Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Guest Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Name:</th>
                                    <td>{{ $Booking->guest_name }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $Booking->guest_email }}</td>
                                </tr>
                                <tr>
                                    <th>Phone:</th>
                                    <td>{{ $Booking->guest_phone }}</td>
                                </tr>
                                <tr>
                                    <th>Address:</th>
                                    <td>{{ $Booking->guest_address ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Guests:</th>
                                    <td>{{ $Booking->adults }} Adults, {{ $Booking->children }} Children</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Booking Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Reference No:</th>
                                    <td><strong>{{ $Booking->reference_number }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Room:</th>
                                    <td>
                                        <span class="badge bg-primary fs-6">Room {{ $Booking->room->RoomNo }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Check In:</th>
                                    <td>{{ $Booking->check_in->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Check Out:</th>
                                    <td>{{ $Booking->check_out->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Nights:</th>
                                    <td>{{ $Booking->nights }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge bg-{{ $Booking->booking_status == 'Confirmed' ? 'primary' : ($Booking->booking_status == 'Checked In' ? 'success' : ($Booking->booking_status == 'Checked Out' ? 'info' : 'danger')) }}">
                                            {{ $Booking->booking_status }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Payment Details</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="50%">Room Price:</th>
                                    <td>${{ number_format($Booking->room_price, 2) }}/night</td>
                                </tr>
                                <tr>
                                    <th>Total Room Charge:</th>
                                    <td>${{ number_format($Booking->room_price * $Booking->nights, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Extra Charges:</th>
                                    <td>${{ number_format($Booking->extra_charges, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Discount:</th>
                                    <td>-${{ number_format($Booking->discount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Tax:</th>
                                    <td>${{ number_format($Booking->tax_amount, 2) }}</td>
                                </tr>
                                <tr class="table-success">
                                    <th><strong>Total Amount:</strong></th>
                                    <td><strong>${{ number_format($Booking->total_amount, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Advance Paid:</th>
                                    <td>${{ number_format($Booking->advance_payment, 2) }}</td>
                                </tr>
                                <tr class="table-warning">
                                    <th><strong>Balance Due:</strong></th>
                                    <td><strong>${{ number_format($Booking->balance_due, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Payment Status:</th>
                                    <td>
                                        <span class="badge bg-{{ $Booking->payment_status == 'Paid' ? 'success' : ($Booking->payment_status == 'Partial' ? 'info' : 'warning') }}">
                                            {{ $Booking->payment_status }}
                                        </span>
                                    </td>
                                </tr>
                            </table>

                            @if($Booking->balance_due > 0 && in_array($Booking->booking_status, ['Confirmed', 'Checked In']))
                            <div class="mt-3">
                                <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#addPaymentModal">
                                    <i class="fa-solid fa-money-bill-wave mr-1"></i>Add Payment
                                </button>
                            </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Additional Information</h5>
                            @if($Booking->special_requests)
                                <div class="alert alert-info">
                                    <strong>Special Requests:</strong><br>
                                    {{ $Booking->special_requests }}
                                </div>
                            @endif
                            <table class="table table-bordered">
                                <tr>
                                    <th>Booking Date:</th>
                                    <td>{{ $Booking->created_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                @if($Booking->cancelled_at)
                                <tr>
                                    <th>Cancelled At:</th>
                                    <td>{{ $Booking->cancelled_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                @endif
                            </table>

                            @if($Booking->transactions->count() > 0)
                            <h6 class="mt-3">Payment History</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Method</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($Booking->transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->transaction_date->format('M d, Y') }}</td>
                                            <td>${{ number_format($transaction->amount, 2) }}</td>
                                            <td>{{ $transaction->payment_method }}</td>
                                            <td>{{ $transaction->notes }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('bookings.index') }}" class="btn btn-secondary">
                            <i class="fa-solid fa-arrow-left mr-1"></i>Back to List
                        </a>
                        @if($Booking->booking_status == 'Checked Out')
                            <button class="btn btn-success" onclick="window.print()">
                                <i class="fa-solid fa-print mr-1"></i>Print Invoice
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Payment Modal -->
<div class="modal fade" id="addPaymentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Payment</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('bookings.addPayment', $Booking->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Amount *</label>
                        <input type="number" name="amount" class="form-control" max="{{ $Booking->balance_due }}" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Payment Method *</label>
                        <select name="payment_method" class="form-control" required>
                            <option value="Cash">Cash</option>
                            <option value="Card">Card</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
