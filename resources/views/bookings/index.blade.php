{{-- resources/views/bookings/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Booking Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Booking Management</h3>
                        <div>
                            <a href="{{ route('bookings.create') }}" class="btn btn-success">
                                <i class="fa fa-plus"></i> New Booking
                            </a>
                            <button class="btn btn-danger" id="deleteAllBtn">
                                <i class="fa fa-trash"></i> Delete All
                            </button>
                            <a href="{{ route('bookings.trash.index') }}" class="btn btn-secondary">
                                <i class="fa fa-trash-alt"></i> View Trash
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($bookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="bookingsTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Guest Name</th>
                                        <th>Room</th>
                                        <th>Check In</th>
                                        <th>Check Out</th>
                                        <th>Total Amount</th>
                                        <th>Paid Amount</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bookings as $index => $booking)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $booking->guest_name }}</td>
                                        <td>{{ $booking->room->RoomNo }} ({{ $booking->room->Type }})</td>
                                        <td>{{ \Carbon\Carbon::parse($booking->check_in)->format('M d, Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') }}</td>
                                        <td>${{ number_format($booking->total_amount, 2) }}</td>
                                        <td>${{ number_format($booking->paid_amount, 2) }}</td>
                                        <td>
                                            <span class="badge
                                                @if($booking->status == 'Confirmed') bg-success
                                                @elseif($booking->status == 'Checked In') bg-primary
                                                @elseif($booking->status == 'Checked Out') bg-info
                                                @elseif($booking->status == 'Cancelled') bg-danger
                                                @else bg-secondary
                                                @endif">
                                                {{ $booking->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge
                                                @if($booking->payment_status == 'Paid') bg-success
                                                @elseif($booking->payment_status == 'Pending') bg-warning
                                                @elseif($booking->payment_status == 'Partial') bg-info
                                                @elseif($booking->payment_status == 'Refunded') bg-secondary
                                                @else bg-secondary
                                                @endif">
                                                {{ $booking->payment_status }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a  href="{{ route('bookings.show', $booking->id) }}" class="btn btn-sm btn-info view-btn" >
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="{{ route('bookings.edit', $booking->id) }}" class="btn btn-sm btn-warning">
                                                    <i class="fa fa-edit"></i>
                                                </a>

                                                {{-- In the actions column of your booking index --}}
                                                @if(!$booking->is_fully_paid && $booking->status != 'Cancelled')
                                                    <a href="{{ route('payments.create', $booking->id) }}" class="btn btn-sm btn-success payment-link" title="Add Payment" onclick="event.stopPropagation()">
                                                        <i class="fas fa-money-bill-wave"></i>
                                                    </a>
                                                @endif

                                                @if($booking->status == 'Confirmed')
                                                    <button class="btn btn-sm btn-success checkin-btn" data-id="{{ $booking->id }}">
                                                        <i class="fa fa-sign-in"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger cancel-btn" data-id="{{ $booking->id }}">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                @elseif($booking->status == 'Checked In')
                                                    <button class="btn btn-sm btn-primary checkout-btn" data-id="{{ $booking->id }}">
                                                        <i class="fa fa-sign-out"></i>
                                                    </button>
                                                @endif
                                                <form method="POST" action="{{ route('bookings.destroy', $booking->id) }}" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit}" class="btn btn-sm btn-danger delete-btn" title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </button>

                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <h5><i class="icon fa fa-info"></i> No Bookings Found!</h5>
                            <p>There are no bookings in the system yet. <a href="{{ route('bookings.create') }}">Create the first booking</a>.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Booking Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewModalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this booking?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete All Modal -->
<div class="modal fade" id="deleteAllModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete All</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete ALL bookings? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteAll">Delete All</button>
            </div>
        </div>
    </div>
</div>

<!-- Check In Modal -->
<div class="modal fade" id="checkinModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Check In</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to check in this guest?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmCheckin">Check In</button>
            </div>
        </div>
    </div>
</div>

<!-- Check Out Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Check Out</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to check out this guest?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmCheckout">Check Out</button>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Booking</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="cancellationReason">Cancellation Reason</label>
                    <textarea class="form-control" id="cancellationReason" rows="3" placeholder="Enter reason for cancellation..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmCancel">Confirm Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Payment</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="paymentAmount">Amount</label>
                    <input type="number" step="0.01" class="form-control" id="paymentAmount" placeholder="Enter payment amount">
                </div>
                <div class="form-group">
                    <label for="paymentType">Payment Type</label>
                    <select class="form-control" id="paymentType">
                        <option value="cash">Cash</option>
                        <option value="card">Credit Card</option>
                        <option value="transfer">Bank Transfer</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmPayment">Add Payment</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/bookings.js') }}"></script>
@endpush
