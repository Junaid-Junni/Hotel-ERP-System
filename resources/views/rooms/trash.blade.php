@extends('layouts.app')

@section('title', 'Room Trash')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Room Trash</h3>
                        <div>
                            <a href="{{ route('rooms.index') }}" class="btn btn-primary">
                                <i class="fa fa-arrow-left"></i> Back to Rooms
                            </a>
                            <button class="btn btn-danger" id="emptyTrashBtn">
                                <i class="fa fa-trash"></i> Empty Trash
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($trashedRooms->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="trashTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Room No</th>
                                        <th>Floor</th>
                                        <th>Type</th>
                                        <th>Price</th>
                                        <th>Capacity</th>
                                        <th>Deleted At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($trashedRooms as $index => $room)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $room->RoomNo }}</td>
                                        <td>{{ $room->Floor }}</td>
                                        <td>{{ $room->Type }}</td>
                                        <td>${{ number_format($room->Price, 2) }}</td>
                                        <td>{{ $room->Capacity }} Person{{ $room->Capacity > 1 ? 's' : '' }}</td>
                                        <td>{{ $room->deleted_at->format('M d, Y h:i A') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-success restore-btn" data-id="{{ $room->id }}" title="Restore">
                                                    <i class="fa fa-undo"></i> Restore
                                                </button>
                                                <button class="btn btn-sm btn-danger permanent-delete-btn" data-id="{{ $room->id }}" title="Delete Permanently">
                                                    <i class="fa fa-trash"></i> Delete
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
                            <h5><i class="icon fa fa-info"></i> Trash is Empty!</h5>
                            <p>There are no rooms in the trash.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Empty Trash Modal -->
<div class="modal fade" id="emptyTrashModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Empty Trash</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to empty the trash? This will permanently delete ALL rooms in trash. This action cannot be undone!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmEmptyTrash">Empty Trash</button>
            </div>
        </div>
    </div>
</div>

<!-- Permanent Delete Modal -->
<div class="modal fade" id="permanentDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Permanent Delete</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to permanently delete this room? This action cannot be undone!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmPermanentDelete">Delete Permanently</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
class TrashManager {
    constructor() {
        this.currentRoomId = null;
        this.init();
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        // Restore room
        $(document).on('click', '.restore-btn', (e) => {
            const roomId = $(e.currentTarget).data('id');
            this.restoreRoom(roomId);
        });

        // Permanent delete room
        $(document).on('click', '.permanent-delete-btn', (e) => {
            const roomId = $(e.currentTarget).data('id');
            this.confirmPermanentDelete(roomId);
        });

        // Empty trash
        $('#emptyTrashBtn').on('click', () => {
            this.confirmEmptyTrash();
        });

        // Confirm permanent delete
        $('#confirmPermanentDelete').on('click', () => {
            this.permanentDeleteRoom();
        });

        // Confirm empty trash
        $('#confirmEmptyTrash').on('click', () => {
            this.emptyTrash();
        });
    }

    async restoreRoom(id) {
        try {
            const response = await $.ajax({
                url: `/rooms/trash/${id}/restore`,
                type: 'POST',
                data: {
                    _token: this.getCsrfToken()
                }
            });

            if (response.success) {
                this.showAlert(response.message, 'success');
                // Reload the page after a short delay
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                this.showAlert(response.message, 'error');
            }
        } catch (error) {
            console.error('Restore error:', error);
            this.showAlert('Error restoring room. Please try again.', 'error');
        }
    }

    confirmPermanentDelete(id) {
        this.currentRoomId = id;
        $('#permanentDeleteModal').modal('show');
    }

    confirmEmptyTrash() {
        $('#emptyTrashModal').modal('show');
    }

    async permanentDeleteRoom() {
        if (!this.currentRoomId) {
            this.showAlert('Error: No room selected for deletion.', 'error');
            return;
        }

        try {
            const response = await $.ajax({
                url: `/rooms/trash/${this.currentRoomId}/destroy`,
                type: 'DELETE',
                data: {
                    _token: this.getCsrfToken()
                }
            });

            if (response.success) {
                this.showAlert(response.message, 'success');
                $('#permanentDeleteModal').modal('hide');

                // Reload the page after a short delay
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                this.showAlert(response.message, 'error');
            }
        } catch (error) {
            console.error('Permanent delete error:', error);
            this.showAlert('Error deleting room. Please try again.', 'error');
        } finally {
            this.currentRoomId = null;
        }
    }

    async emptyTrash() {
        try {
            const response = await $.ajax({
                url: '/rooms/trash/empty',
                type: 'DELETE',
                data: {
                    _token: this.getCsrfToken()
                }
            });

            if (response.success) {
                this.showAlert(response.message, 'success');
                $('#emptyTrashModal').modal('hide');

                // Reload the page after a short delay
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                this.showAlert(response.message, 'error');
            }
        } catch (error) {
            console.error('Empty trash error:', error);
            this.showAlert('Error emptying trash. Please try again.', 'error');
        }
    }

    getCsrfToken() {
        return $('meta[name="csrf-token"]').attr('content');
    }

    showAlert(message, type = 'info') {
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[type] || 'alert-info';

        // Remove existing alerts
        $('.alert-dismissible').remove();

        const alert = $(`
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `);

        $('.card-body').prepend(alert);

        setTimeout(() => {
            alert.alert('close');
        }, 5000);
    }
}

// Initialize trash manager when document is ready
$(document).ready(function () {
    window.trashManager = new TrashManager();
});
</script>
@endpush
