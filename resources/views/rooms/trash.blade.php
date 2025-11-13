@extends('layouts.app')

@section('title', 'Room Trash')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fa fa-trash-alt"></i> Room Trash
                        </h3>
                        <div class="btn-group">
                            <a href="{{ route('rooms.index') }}" class="btn btn-light">
                                <i class="fa fa-arrow-left"></i> Back to Rooms
                            </a>
                            <button class="btn btn-danger" id="emptyTrashBtn">
                                <i class="fa fa-trash"></i> Empty Trash
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fa fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if($trashedRooms->count() > 0)
                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle"></i>
                            <strong>Note:</strong> Deleted rooms are kept in trash for 30 days before being permanently deleted.
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Room No</th>
                                        <th>Floor</th>
                                        <th>Type</th>
                                        <th>Price</th>
                                        <th>Capacity</th>
                                        <th>Status</th>
                                        <th>Deleted At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($trashedRooms as $index => $room)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $room->RoomNo }}</strong>
                                            @if($room->Images)
                                                <br><small class="text-muted">{{ count($room->Images) }} image(s)</small>
                                            @endif
                                        </td>
                                        <td>{{ $room->Floor }}</td>
                                        <td>{{ $room->Type }}</td>
                                        <td>${{ number_format($room->Price, 2) }}</td>
                                        <td>{{ $room->Capacity }} Person{{ $room->Capacity > 1 ? 's' : '' }}</td>
                                        <td>
                                            <span class="badge
                                                @if($room->Status == 'Available') bg-success
                                                @elseif($room->Status == 'Occupied') bg-danger
                                                @elseif($room->Status == 'Maintenance') bg-warning
                                                @elseif($room->Status == 'Cleaning') bg-info
                                                @else bg-secondary
                                                @endif">
                                                {{ $room->Status }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $room->deleted_at->format('M d, Y h:i A') }}
                                            <br>
                                            <small class="text-muted">
                                                {{ $room->deleted_at->diffForHumans() }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-info view-btn" data-id="{{ $room->id }}" title="View Details">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-success restore-btn" data-id="{{ $room->id }}" title="Restore">
                                                    <i class="fa fa-undo"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger permanent-delete-btn" data-id="{{ $room->id }}" title="Delete Permanently">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Trash Statistics -->
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info"><i class="fa fa-trash"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total in Trash</span>
                                        <span class="info-box-number">{{ $trashedRooms->count() }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning"><i class="fa fa-clock"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Oldest Item</span>
                                        <span class="info-box-number">
                                            @if($trashedRooms->count() > 0)
                                                {{ $trashedRooms->sortBy('deleted_at')->first()->deleted_at->diffForHumans() }}
                                            @else
                                                N/A
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success"><i class="fa fa-calendar"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Auto Cleanup</span>
                                        <span class="info-box-number">30 days</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <h5><i class="fa fa-trash-alt fa-3x mb-3"></i></h5>
                            <h5>Trash is Empty!</h5>
                            <p class="text-muted">There are no deleted rooms in the trash.</p>
                            <a href="{{ route('rooms.index') }}" class="btn btn-primary">
                                <i class="fa fa-arrow-left"></i> Back to Rooms
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Room Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewModalBody">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Empty Trash Modal -->
<div class="modal fade" id="emptyTrashModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Empty Trash</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to empty the trash?</p>
                <p class="text-danger">
                    <i class="fa fa-exclamation-triangle"></i>
                    <strong>This action will permanently delete ALL {{ $trashedRooms->count() }} rooms in trash!</strong>
                </p>
                <p class="text-muted">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmEmptyTrash">Empty Trash</button>
            </div>
        </div>
    </div>
</div>

<!-- Permanent Delete Modal -->
<div class="modal fade" id="permanentDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Permanent Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to permanently delete this room?</p>
                <p class="text-danger">
                    <i class="fa fa-exclamation-triangle"></i>
                    <strong>This action cannot be undone!</strong>
                </p>
                <p class="text-muted">All room images and data will be permanently lost.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmPermanentDelete">Delete Permanently</button>
            </div>
        </div>
    </div>
</div>

<!-- Restore Modal -->
<div class="modal fade" id="restoreModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Restore</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to restore this room?</p>
                <p class="text-info">
                    <i class="fa fa-info-circle"></i>
                    The room will be moved back to the active rooms list.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmRestore">Restore Room</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Room Trash Manager initialized');

    let currentRoomId = null;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // View Room
    document.querySelectorAll('.view-btn').forEach(button => {
        button.addEventListener('click', function() {
            const roomId = this.getAttribute('data-id');
            viewRoom(roomId);
        });
    });

    // Restore Room
    document.querySelectorAll('.restore-btn').forEach(button => {
        button.addEventListener('click', function() {
            const roomId = this.getAttribute('data-id');
            confirmRestore(roomId);
        });
    });

    // Permanent Delete Room
    document.querySelectorAll('.permanent-delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const roomId = this.getAttribute('data-id');
            confirmPermanentDelete(roomId);
        });
    });

    // Empty Trash
    const emptyTrashBtn = document.getElementById('emptyTrashBtn');
    if (emptyTrashBtn) {
        emptyTrashBtn.addEventListener('click', confirmEmptyTrash);
    }

    // Confirm Restore
    const confirmRestoreBtn = document.getElementById('confirmRestore');
    if (confirmRestoreBtn) {
        confirmRestoreBtn.addEventListener('click', restoreRoom);
    }

    // Confirm Permanent Delete
    const confirmPermanentDeleteBtn = document.getElementById('confirmPermanentDelete');
    if (confirmPermanentDeleteBtn) {
        confirmPermanentDeleteBtn.addEventListener('click', permanentDeleteRoom);
    }

    // Confirm Empty Trash
    const confirmEmptyTrashBtn = document.getElementById('confirmEmptyTrash');
    if (confirmEmptyTrashBtn) {
        confirmEmptyTrashBtn.addEventListener('click', emptyTrash);
    }

    async function viewRoom(id) {
        try {
            console.log('Fetching room details for ID:', id);

            const response = await fetch(`/rooms/${id}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();
            console.log('View room response:', data);

            if (data.success) {
                displayRoomDetails(data.room);
            } else {
                showAlert('Error loading room details: ' + (data.message || 'Unknown error'), 'error');
            }
        } catch (error) {
            console.error('Error in viewRoom:', error);
            showAlert('Error loading room details. Please try again.', 'error');
        }
    }

    function displayRoomDetails(room) {
        console.log('Displaying room details:', room);

        const amenities = getAmenitiesList(room);
        const images = room.Images ? (typeof room.Images === 'string' ? JSON.parse(room.Images) : room.Images) : [];

        let imagesHtml = '';
        if (images.length > 0) {
            imagesHtml = `
                <div class="mt-3">
                    <h6>Room Images:</h6>
                    <div class="row">
                        ${images.map(img => `
                            <div class="col-md-4 mb-2">
                                <img src="/storage/${img}" class="img-fluid rounded" alt="Room Image" style="max-height: 150px; object-fit: cover;">
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        }

        const deletedAt = room.deleted_at
            ? new Date(room.deleted_at).toLocaleString()
            : 'N/A';

        const content = `
            <div class="alert alert-warning">
                <i class="fa fa-trash"></i>
                <strong>This room is in trash.</strong> Deleted on: ${deletedAt}
            </div>

            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-sm">
                        <tr>
                            <th class="w-40">Room Number:</th>
                            <td>${room.RoomNo}</td>
                        </tr>
                        <tr>
                            <th>Floor:</th>
                            <td>${room.Floor}</td>
                        </tr>
                        <tr>
                            <th>Type:</th>
                            <td>${room.Type}</td>
                        </tr>
                        <tr>
                            <th>Price:</th>
                            <td>$${parseFloat(room.Price).toFixed(2)}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered table-sm">
                        <tr>
                            <th class="w-40">Capacity:</th>
                            <td>${room.Capacity} Person${room.Capacity > 1 ? 's' : ''}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td><span class="badge ${getStatusBadgeClass(room.Status)}">${room.Status}</span></td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td>${new Date(room.created_at).toLocaleDateString()}</td>
                        </tr>
                        <tr>
                            <th>Deleted:</th>
                            <td>${new Date(room.deleted_at).toLocaleDateString()}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <h6>Amenities:</h6>
                    ${amenities}
                </div>
            </div>
            ${room.Description ? `
            <div class="row mt-3">
                <div class="col-md-12">
                    <h6>Description:</h6>
                    <p class="p-2 bg-light rounded">${room.Description}</p>
                </div>
            </div>
            ` : '<div class="row mt-3"><div class="col-md-12"><p class="text-muted">No description provided.</p></div></div>'}
            ${imagesHtml}
        `;

        document.getElementById('viewModalBody').innerHTML = content;
        $('#viewModal').modal('show');
    }

    function getAmenitiesList(room) {
        const amenities = [];
        const amenityMap = {
            AC: 'Air Conditioning',
            TV: 'TV',
            WiFi: 'WiFi',
            Geyser: 'Geyser',
            Balcony: 'Balcony',
            Intercom: 'Intercom',
            RoomService: 'Room Service',
            Minibar: 'Minibar'
        };

        Object.keys(amenityMap).forEach(key => {
            if (room[key]) {
                amenities.push(amenityMap[key]);
            }
        });

        if (amenities.length === 0) {
            return '<span class="text-muted">No amenities</span>';
        }

        return `
            <div class="row">
                ${amenities.map(amenity => `
                    <div class="col-md-6 mb-1">
                        <i class="fa fa-check text-success mr-2"></i> ${amenity}
                    </div>
                `).join('')}
            </div>
        `;
    }

    function getStatusBadgeClass(status) {
        const classes = {
            'Available': 'bg-success',
            'Occupied': 'bg-danger',
            'Maintenance': 'bg-warning',
            'Cleaning': 'bg-info'
        };
        return classes[status] || 'bg-secondary';
    }

    function confirmRestore(id) {
        currentRoomId = id;
        $('#restoreModal').modal('show');
    }

    function confirmPermanentDelete(id) {
        currentRoomId = id;
        $('#permanentDeleteModal').modal('show');
    }

    function confirmEmptyTrash() {
        $('#emptyTrashModal').modal('show');
    }

    async function restoreRoom() {
        if (!currentRoomId) {
            showAlert('Error: No room selected for restoration.', 'error');
            return;
        }

        try {
            const response = await fetch(`/rooms/trash/${currentRoomId}/restore`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                showAlert(data.message, 'success');
                $('#restoreModal').modal('hide');
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert(data.message, 'error');
            }
        } catch (error) {
            console.error('Restore error:', error);
            showAlert('Error restoring room. Please try again.', 'error');
        } finally {
            currentRoomId = null;
        }
    }

    async function permanentDeleteRoom() {
        if (!currentRoomId) {
            showAlert('Error: No room selected for deletion.', 'error');
            return;
        }

        try {
            const response = await fetch(`/rooms/trash/${currentRoomId}/destroy`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                showAlert(data.message, 'success');
                $('#permanentDeleteModal').modal('hide');
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert(data.message, 'error');
            }
        } catch (error) {
            console.error('Permanent delete error:', error);
            showAlert('Error deleting room. Please try again.', 'error');
        } finally {
            currentRoomId = null;
        }
    }

    async function emptyTrash() {
        try {
            const response = await fetch('/rooms/trash/empty', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                showAlert(data.message, 'success');
                $('#emptyTrashModal').modal('hide');
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert(data.message, 'error');
            }
        } catch (error) {
            console.error('Empty trash error:', error);
            showAlert('Error emptying trash. Please try again.', 'error');
        }
    }

    function showAlert(message, type = 'info') {
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[type] || 'alert-info';

        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.alert-dismissible');
        existingAlerts.forEach(alert => alert.remove());

        const alert = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;

        const cardBody = document.querySelector('.card-body');
        if (cardBody) {
            cardBody.insertAdjacentHTML('afterbegin', alert);
        }

        setTimeout(() => {
            const alertElement = document.querySelector('.alert-dismissible');
            if (alertElement) {
                alertElement.remove();
            }
        }, 5000);
    }
});
</script>
@endpush
