@extends('layouts.app')

@section('title', 'Payment Details - ' . $payment->payment_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">
                        <i class="fas fa-receipt"></i> Payment Receipt - {{ $payment->payment_number }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('bookings.show', $payment->booking_id) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Booking
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Payment Number:</th>
                                    <td>{{ $payment->payment_number }}</td>
                                </tr>
                                <tr>
                                    <th>Booking Reference:</th>
                                    <td>
                                        <a href="{{ route('bookings.show', $payment->booking_id) }}">
                                            {{ $payment->booking->guest_name }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Room:</th>
                                    <td>{{ $payment->booking->room->RoomNo }} ({{ $payment->booking->room->Type }})</td>
                                </tr>
                                <tr>
                                    <th>Payment Date:</th>
                                    <td>{{ $payment->payment_date->format('F d, Y') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Amount:</th>
                                    <td class="font-weight-bold">${{ number_format($payment->amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Payment Method:</th>
                                    <td>
                                        <span class="badge badge-secondary">{{ ucfirst($payment->payment_method) }}</span>
                                        @if($payment->digital_type)
                                            <span class="badge badge-info ml-1">{{ $payment->digital_type_text }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge badge-{{ $payment->status_badge }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @if($payment->transaction_id)
                                <tr>
                                    <th>Transaction ID:</th>
                                    <td>{{ $payment->transaction_id }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    @if($payment->notes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5>Notes:</h5>
                            <div class="alert alert-light">
                                {{ $payment->notes }}
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Payment Proof Section -->
                    @if($payment->screenshot_path)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Payment Proof:</h5>
                            <div class="text-center">
                                <img src="{{ Storage::url($payment->screenshot_path) }}"
                                     alt="Payment Proof"
                                     class="img-fluid rounded shadow-sm"
                                     style="max-height: 400px;">
                                <div class="mt-2">
                                    <a href="{{ route('payments.download-proof', $payment->id) }}"
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-download"></i> Download Proof
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">
                            Created: {{ $payment->created_at->format('M d, Y h:i A') }}
                        </small>
                        <div>
                            <button class="btn btn-danger btn-sm" onclick="confirmDelete({{ $payment->id }})">
                                <i class="fas fa-trash"></i> Delete Payment
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Summary -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h3 class="card-title">Booking Summary</h3>
                </div>
                <div class="card-body">
                    <h6>Guest Information</h6>
                    <p>
                        <strong>{{ $payment->booking->guest_name }}</strong><br>
                        {{ $payment->booking->guest_email }}<br>
                        {{ $payment->booking->guest_phone }}
                    </p>

                    <h6>Stay Details</h6>
                    <p>
                        Check In: {{ $payment->booking->check_in->format('M d, Y') }}<br>
                        Check Out: {{ $payment->booking->check_out->format('M d, Y') }}<br>
                        Nights: {{ $payment->booking->total_nights }}
                    </p>

                    <h6>Financial Summary</h6>
                    <table class="table table-sm">
                        <tr>
                            <td>Total Amount:</td>
                            <td class="text-right">${{ number_format($payment->booking->total_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Total Paid:</td>
                            <td class="text-right">${{ number_format($payment->booking->total_paid, 2) }}</td>
                        </tr>
                        <tr class="table-warning">
                            <td><strong>Remaining:</strong></td>
                            <td class="text-right"><strong>${{ number_format($payment->booking->remaining_amount, 2) }}</strong></td>
                        </tr>
                    </table>
                </div>
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
                <p>Are you sure you want to delete this payment? This action cannot be undone.</p>
                <p class="text-danger"><strong>Warning:</strong> This will affect the booking's payment status.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete Payment</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentPaymentId = null;

function confirmDelete(paymentId) {
    currentPaymentId = paymentId;
    $('#deleteModal').modal('show');
}

document.getElementById('confirmDelete').addEventListener('click', function() {
    if (!currentPaymentId) return;

    fetch(`/payments/${currentPaymentId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '{{ route("bookings.show", $payment->booking_id) }}';
        } else {
            alert('Error deleting payment');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting payment');
    });
});
</script>
@endpush
