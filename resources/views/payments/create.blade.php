@extends('layouts.app')

@section('title', 'Add Payment - ' . $booking->guest_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">
                        <i class="fas fa-credit-card"></i> Add Payment
                    </h3>
                    <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-light btn-sm float-right">
                        <i class="fas fa-arrow-left"></i> Back to Booking
                    </a>
                </div>
                <div class="card-body">
                    <!-- Booking Summary -->
                    <div class="alert alert-info">
                        <h5 class="alert-heading">Booking Summary</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Guest:</strong> {{ $booking->guest_name }}<br>
                                <strong>Room:</strong> {{ $booking->room->RoomNo }} ({{ $booking->room->Type }})<br>
                                <strong>Stay:</strong> {{ $booking->total_nights }} nights
                            </div>
                            <div class="col-md-6">
                                <strong>Total Amount:</strong> ${{ number_format($booking->total_amount, 2) }}<br>
                                <strong>Paid Amount:</strong> ${{ number_format($booking->total_paid, 2) }}<br>
                                <strong>Remaining:</strong> <span class="font-weight-bold">${{ number_format($booking->remaining_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('payments.store', $booking->id) }}" method="POST" enctype="multipart/form-data" id="paymentForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="amount">Payment Amount *</label>
                                    <input type="number" step="0.01" class="form-control" id="amount" name="amount"
                                           value="{{ old('amount', min(100, $booking->remaining_amount)) }}"
                                           max="{{ $booking->remaining_amount }}" required>
                                    <small class="form-text text-muted">Maximum: ${{ number_format($booking->remaining_amount, 2) }}</small>
                                </div>

                                <div class="form-group">
                                    <label for="payment_date">Payment Date *</label>
                                    <input type="date" class="form-control" id="payment_date" name="payment_date"
                                           value="{{ old('payment_date', date('Y-m-d')) }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="payment_method">Payment Method *</label>
                                    <select class="form-control" id="payment_method" name="payment_method" required>
                                        <option value="">Select Method</option>
                                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="digital" {{ old('payment_method') == 'digital' ? 'selected' : '' }}>Digital Payment</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Digital Payment Fields (Initially Hidden) -->
                                <div id="digitalFields" style="display: none;">
                                    <div class="form-group">
                                        <label for="digital_type">Digital Payment Type *</label>
                                        <select class="form-control" id="digital_type" name="digital_type">
                                            <option value="">Select Type</option>
                                            <option value="bank_transfer" {{ old('digital_type') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                            <option value="credit_card" {{ old('digital_type') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                            <option value="debit_card" {{ old('digital_type') == 'debit_card' ? 'selected' : '' }}>Debit Card</option>
                                            <option value="e_wallet" {{ old('digital_type') == 'e_wallet' ? 'selected' : '' }}>E-Wallet</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="transaction_id">Transaction ID</label>
                                        <input type="text" class="form-control" id="transaction_id" name="transaction_id"
                                               value="{{ old('transaction_id') }}" placeholder="Enter transaction reference">
                                    </div>

                                    <div class="form-group">
                                        <label for="screenshot">Payment Proof (Screenshot)</label>
                                        <input type="file" class="form-control-file" id="screenshot" name="screenshot" accept="image/*">
                                        <small class="form-text text-muted">Upload screenshot of payment confirmation (max: 5MB)</small>
                                    </div>

                                    <!-- Preview for screenshot -->
                                    <div id="screenshotPreview" class="mt-2" style="display: none;">
                                        <img id="previewImage" src="#" alt="Preview" class="img-thumbnail" style="max-height: 150px;">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"
                                              placeholder="Any additional notes about this payment...">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-check-circle"></i> Process Payment
                            </button>
                            <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Payment History -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i> Payment History
                    </h3>
                </div>
                <div class="card-body">
                    @if($booking->payments->count() > 0)
                        <div class="list-group">
                            @foreach($booking->payments->sortByDesc('created_at') as $payment)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1">${{ number_format($payment->amount, 2) }}</h6>
                                    <small class="text-muted">{{ $payment->payment_date->format('M d, Y') }}</small>
                                </div>
                                <p class="mb-1">
                                    <span class="badge badge-{{ $payment->status_badge }}">{{ ucfirst($payment->status) }}</span>
                                    <span class="badge badge-secondary">{{ ucfirst($payment->payment_method) }}</span>
                                    @if($payment->digital_type)
                                        <span class="badge badge-info">{{ $payment->digital_type_text }}</span>
                                    @endif
                                </p>
                                @if($payment->notes)
                                    <small class="text-muted">{{ Str::limit($payment->notes, 50) }}</small>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center">No payments recorded yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethod = document.getElementById('payment_method');
    const digitalFields = document.getElementById('digitalFields');
    const screenshotInput = document.getElementById('screenshot');
    const previewImage = document.getElementById('previewImage');
    const screenshotPreview = document.getElementById('screenshotPreview');

    // Toggle digital fields based on payment method
    paymentMethod.addEventListener('change', function() {
        if (this.value === 'digital') {
            digitalFields.style.display = 'block';
            // Make digital type required
            document.getElementById('digital_type').required = true;
        } else {
            digitalFields.style.display = 'none';
            // Remove required from digital type
            document.getElementById('digital_type').required = false;
        }
    });

    // Trigger change event on page load
    paymentMethod.dispatchEvent(new Event('change'));

    // Preview screenshot before upload
    screenshotInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                screenshotPreview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            screenshotPreview.style.display = 'none';
        }
    });

    // Form validation
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        const amount = parseFloat(document.getElementById('amount').value);
        const remaining = parseFloat({{ $booking->remaining_amount }});

        if (amount > remaining) {
            e.preventDefault();
            alert('Payment amount cannot exceed remaining amount of $' + remaining.toFixed(2));
            return false;
        }
    });
});
</script>
@endpush
