{{-- resources/views/bookings/trash.blade.php --}}
@extends('layouts.app')

@section('title', 'Trashed Bookings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Trashed Bookings</h3>
                        <a href="{{ route('bookings.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back to Bookings
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($trashedBookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Guest Name</th>
                                        <th>Room</th>
                                        <th>Check In</th>
                                        <th>Check Out</th>
                                        <th>Total Amount</th>
                                        <th>Status</th>
                                        <th>Deleted At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($trashedBookings as $booking)
                                    <tr>
                                        <td>{{ $booking->guest_name }}</td>
                                        <td>{{ $booking->room->RoomNo }} ({{ $booking->room->Type }})</td>
                                        <td>{{ \Carbon\Carbon::parse($booking->check_in)->format('M d, Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') }}</td>
                                        <td>${{ number_format($booking->total_amount, 2) }}</td>
                                        <td>
                                            <span class="badge badge-{{ $booking->status == 'Confirmed' ? 'success' : ($booking->status == 'Checked In' ? 'primary' : ($booking->status == 'Checked Out' ? 'info' : 'danger')) }}">
                                                {{ $booking->status }}
                                            </span>
                                        </td>
                                        <td>{{ $booking->deleted_at->format('M d, Y h:i A') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-success restore-btn" data-id="{{ $booking->id }}">
                                                    <i class="fa fa-undo"></i> Restore
                                                </button>
                                                <button class="btn btn-sm btn-danger force-delete-btn" data-id="{{ $booking->id }}">
                                                    <i class="fa fa-trash"></i> Delete Permanently
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <h5><i class="icon fa fa-info"></i> No Trashed Bookings!</h5>
                            <p>There are no bookings in the trash bin.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Restore Modal -->
<div class="modal fade" id="restoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Restore</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to restore this booking?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmRestore">Restore</button>
            </div>
        </div>
    </div>
</div>

<!-- Force Delete Modal -->
<div class="modal fade" id="forceDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Permanent Delete</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to permanently delete this booking? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmForceDelete">Delete Permanently</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let currentBookingId = null;

    // Restore booking
    $('.restore-btn').click(function() {
        currentBookingId = $(this).data('id');
        $('#restoreModal').modal('show');
    });

    $('#confirmRestore').click(function() {
        if (!currentBookingId) return;

        $.ajax({
            url: "{{ url('bookings/trash') }}/" + currentBookingId + "/restore",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    $('#restoreModal').modal('hide');
                    location.reload();
                }
            }
        });
    });

    // Force delete booking
    $('.force-delete-btn').click(function() {
        currentBookingId = $(this).data('id');
        $('#forceDeleteModal').modal('show');
    });

    $('#confirmForceDelete').click(function() {
        if (!currentBookingId) return;

        $.ajax({
            url: "{{ url('bookings/trash') }}/" + currentBookingId + "/force",
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    $('#forceDeleteModal').modal('hide');
                    location.reload();
                }
            }
        });
    });
});
</script>
@endpush
