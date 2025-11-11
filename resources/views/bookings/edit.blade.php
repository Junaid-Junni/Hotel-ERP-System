{{-- resources/views/bookings/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Booking')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Edit Booking: {{ $booking->guest_name }}</h3>
                        <a href="{{ route('bookings.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back to Bookings
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('bookings.update', $booking->id) }}" method="POST" id="editForm">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="room_id">Room *</label>
                                    <select class="form-control @error('room_id') is-invalid @enderror" id="room_id" name="room_id" required>
                                        <option value="">Select Room</option>
                                        @foreach($rooms as $room)
                                            <option value="{{ $room->id }}"
                                                data-price="{{ $room->Price }}"
                                                {{ old('room_id', $booking->room_id) == $room->id ? 'selected' : '' }}>
                                                Room {{ $room->RoomNo }} - {{ $room->Type }} ({{ $room->Floor }}) - ${{ $room->Price }}/night
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('room_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="guest_name">Guest Name *</label>
                                    <input type="text" class="form-control @error('guest_name') is-invalid @enderror"
                                           id="guest_name" name="guest_name" value="{{ old('guest_name', $booking->guest_name) }}" required>
                                    @error('guest_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="guest_email">Guest Email *</label>
                                    <input type="email" class="form-control @error('guest_email') is-invalid @enderror"
                                           id="guest_email" name="guest_email" value="{{ old('guest_email', $booking->guest_email) }}" required>
                                    @error('guest_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="guest_phone">Guest Phone *</label>
                                    <input type="text" class="form-control @error('guest_phone') is-invalid @enderror"
                                           id="guest_phone" name="guest_phone" value="{{ old('guest_phone', $booking->guest_phone) }}" required>
                                    @error('guest_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="check_in">Check In Date *</label>
                                    <input type="date" class="form-control @error('check_in') is-invalid @enderror"
                                           id="check_in" name="check_in" value="{{ old('check_in', $booking->check_in->format('Y-m-d')) }}" required>
                                    @error('check_in')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="check_out">Check Out Date *</label>
                                    <input type="date" class="form-control @error('check_out') is-invalid @enderror"
                                           id="check_out" name="check_out" value="{{ old('check_out', $booking->check_out->format('Y-m-d')) }}" required>
                                    @error('check_out')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="adults">Adults *</label>
                                    <select class="form-control @error('adults') is-invalid @enderror" id="adults" name="adults" required>
                                        <option value="">Select Adults</option>
                                        @for($i = 1; $i <= 10; $i++)
                                            <option value="{{ $i }}" {{ old('adults', $booking->adults) == $i ? 'selected' : '' }}>{{ $i }} Adult{{ $i > 1 ? 's' : '' }}</option>
                                        @endfor
                                    </select>
                                    @error('adults')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="children">Children</label>
                                    <select class="form-control @error('children') is-invalid @enderror" id="children" name="children">
                                        <option value="0">No Children</option>
                                        @for($i = 1; $i <= 10; $i++)
                                            <option value="{{ $i }}" {{ old('children', $booking->children) == $i ? 'selected' : '' }}>{{ $i }} Child{{ $i > 1 ? 'ren' : '' }}</option>
                                        @endfor
                                    </select>
                                    @error('children')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="guest_address">Guest Address</label>
                                    <textarea class="form-control @error('guest_address') is-invalid @enderror"
                                              id="guest_address" name="guest_address" rows="2">{{ old('guest_address', $booking->guest_address) }}</textarea>
                                    @error('guest_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="special_requests">Special Requests</label>
                                    <textarea class="form-control @error('special_requests') is-invalid @enderror"
                                              id="special_requests" name="special_requests" rows="3">{{ old('special_requests', $booking->special_requests) }}</textarea>
                                    @error('special_requests')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Booking Summary -->
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h5>Booking Summary</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <p><strong>Total Nights:</strong> <span id="total_nights">{{ $booking->total_nights }}</span></p>
                                            </div>
                                            <div class="col-md-3">
                                                <p><strong>Room Price:</strong> $<span id="room_price">{{ number_format($booking->room->Price, 2) }}</span>/night</p>
                                            </div>
                                            <div class="col-md-3">
                                                <p><strong>Total Amount:</strong> $<span id="total_amount">{{ number_format($booking->total_amount, 2) }}</span></p>
                                            </div>
                                            <div class="col-md-3">
                                                <p><strong>Status:</strong> <span class="badge badge-{{ $booking->status == 'Confirmed' ? 'success' : ($booking->status == 'Checked In' ? 'primary' : ($booking->status == 'Checked Out' ? 'info' : 'danger')) }}">{{ $booking->status }}</span></p>
                                            </div>
                                        </div>
                                        <div class="alert alert-info mt-2" id="availability_message" style="display: none;">
                                            <!-- Availability message will appear here -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fa fa-save"></i> Update Booking
                                </button>
                                <a href="{{ route('bookings.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Check availability and calculate total when dates or room change
    $('#room_id, #check_in, #check_out').change(function() {
        checkAvailabilityAndCalculate();
    });

    function checkAvailabilityAndCalculate() {
        const roomId = $('#room_id').val();
        const checkIn = $('#check_in').val();
        const checkOut = $('#check_out').val();
        const bookingId = {{ $booking->id }};

        if (roomId && checkIn && checkOut) {
            $.ajax({
                url: "{{ route('bookings.check-availability') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    room_id: roomId,
                    check_in: checkIn,
                    check_out: checkOut,
                    booking_id: bookingId
                },
                success: function(response) {
                    if (response.success) {
                        if (response.available) {
                            $('#total_nights').text(response.total_nights);
                            $('#room_price').text(response.room_price);
                            $('#total_amount').text(response.total_amount);
                            $('#availability_message').removeClass('alert-danger').addClass('alert-success')
                                .html('<i class="fa fa-check"></i> Room is available for selected dates.')
                                .show();
                            $('#submitBtn').prop('disabled', false);
                        } else {
                            $('#availability_message').removeClass('alert-success').addClass('alert-danger')
                                .html('<i class="fa fa-times"></i> ' + response.message)
                                .show();
                            $('#submitBtn').prop('disabled', true);
                        }
                    }
                }
            });
        }
    }

    // Initial calculation if values exist
    if ($('#room_id').val() && $('#check_in').val() && $('#check_out').val()) {
        checkAvailabilityAndCalculate();
    }
});
</script>
@endpush
