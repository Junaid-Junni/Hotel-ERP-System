// public/js/rooms.js

document.addEventListener('DOMContentLoaded', function () {
    console.log('Room Manager initialized');

    let currentRoomId = null;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // View Room
    document.querySelectorAll('.view-btn').forEach(button => {
        button.addEventListener('click', function () {
            const roomId = this.getAttribute('data-id');
            viewRoom(roomId);
        });
    });

    // Delete Room
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function () {
            const roomId = this.getAttribute('data-id');
            confirmDelete(roomId);
        });
    });

    // Delete All Rooms
    const deleteAllBtn = document.getElementById('deleteAllBtn');
    if (deleteAllBtn) {
        deleteAllBtn.addEventListener('click', confirmDeleteAll);
    }

    // Confirm Delete
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', deleteRoom);
    }

    // Confirm Delete All
    const confirmDeleteAllBtn = document.getElementById('confirmDeleteAll');
    if (confirmDeleteAllBtn) {
        confirmDeleteAllBtn.addEventListener('click', deleteAllRooms);
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

        const content = `
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
                            <th>Updated:</th>
                            <td>${new Date(room.updated_at).toLocaleDateString()}</td>
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

    function confirmDelete(id) {
        currentRoomId = id;
        console.log('Setting currentRoomId for deletion:', currentRoomId);
        $('#deleteModal').modal('show');
    }

    function confirmDeleteAll() {
        $('#deleteAllModal').modal('show');
    }

    async function deleteRoom() {
        if (!currentRoomId) {
            console.error('No room ID set for deletion');
            showAlert('Error: No room selected for deletion.', 'error');
            return;
        }

        console.log('Deleting room with ID:', currentRoomId);

        try {
            const response = await fetch(`/rooms/${currentRoomId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            console.log('Delete response:', data);

            if (data.success) {
                showAlert(data.message, 'success');
                $('#deleteModal').modal('hide');

                // Reload the page after a short delay
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showAlert(data.message, 'error');
            }
        } catch (error) {
            console.error('Delete error:', error);
            showAlert('Error deleting room. Please try again.', 'error');
        } finally {
            currentRoomId = null;
        }
    }

    async function deleteAllRooms() {
        try {
            const response = await fetch('/rooms', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            console.log('Delete all response:', data);

            if (data.success) {
                showAlert(data.message, 'success');
                $('#deleteAllModal').modal('hide');

                // Reload the page after a short delay
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showAlert(data.message, 'error');
            }
        } catch (error) {
            console.error('Delete all error:', error);
            showAlert('Error deleting all rooms. Please try again.', 'error');
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
        cardBody.insertAdjacentHTML('afterbegin', alert);

        setTimeout(() => {
            const alertElement = document.querySelector('.alert-dismissible');
            if (alertElement) {
                alertElement.remove();
            }
        }, 5000);
    }
});
