// public/js/rooms.js

class RoomManager {
    constructor() {
        this.currentRoomId = null;
        this.init();
    }

    init() {
        this.bindEvents();
        console.log('RoomManager initialized');
    }

    bindEvents() {
        console.log('Binding events...');

        // View room
        $(document).on('click', '.view-btn', (e) => {
            console.log('View button clicked');
            const roomId = $(e.currentTarget).data('id');
            console.log('Room ID:', roomId);
            this.viewRoom(roomId);
        });

        // Delete room
        $(document).on('click', '.delete-btn', (e) => {
            console.log('Delete button clicked');
            const roomId = $(e.currentTarget).data('id');
            console.log('Room ID:', roomId);
            this.confirmDelete(roomId);
        });

        // Delete all rooms
        $('#deleteAllBtn').on('click', () => {
            console.log('Delete All button clicked');
            this.confirmDeleteAll();
        });

        // Confirm delete
        $('#confirmDelete').on('click', () => {
            console.log('Confirm Delete clicked');
            this.deleteRoom();
        });

        // Confirm delete all
        $('#confirmDeleteAll').on('click', () => {
            console.log('Confirm Delete All clicked');
            this.deleteAllRooms();
        });

        console.log('All events bound successfully');
    }

    async viewRoom(id) {
        try {
            console.log('Fetching room details for ID:', id);

            const response = await $.ajax({
                url: `/rooms/${id}`,
                type: 'GET',
                dataType: 'json'
            });

            console.log('View room response:', response);

            if (response.success) {
                this.displayRoomDetails(response.room);
            } else {
                this.showAlert('Error loading room details: ' + (response.message || 'Unknown error'), 'error');
            }
        } catch (error) {
            console.error('Error in viewRoom:', error);
            this.showAlert('Error loading room details. Please try again.', 'error');
        }
    }

    displayRoomDetails(room) {
        console.log('Displaying room details:', room);

        const amenities = this.getAmenitiesList(room);
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
                            <td><span class="badge ${this.getStatusBadgeClass(room.Status)}">${room.Status}</span></td>
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

        $('#viewModalBody').html(content);
        $('#viewModal').modal('show');
    }

    getAmenitiesList(room) {
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

    getStatusBadgeClass(status) {
        const classes = {
            'Available': 'bg-success',
            'Occupied': 'bg-danger',
            'Maintenance': 'bg-warning',
            'Cleaning': 'bg-info'
        };
        return classes[status] || 'bg-secondary';
    }

    confirmDelete(id) {
        this.currentRoomId = id;
        console.log('Setting currentRoomId for deletion:', this.currentRoomId);
        $('#deleteModal').modal('show');
    }

    confirmDeleteAll() {
        $('#deleteAllModal').modal('show');
    }

    async deleteRoom() {
        if (!this.currentRoomId) {
            console.error('No room ID set for deletion');
            this.showAlert('Error: No room selected for deletion.', 'error');
            return;
        }

        console.log('Deleting room with ID:', this.currentRoomId);

        try {
            const response = await $.ajax({
                url: `/rooms/${this.currentRoomId}`,
                type: 'DELETE',
                data: {
                    _token: this.getCsrfToken()
                }
            });

            console.log('Delete response:', response);

            if (response.success) {
                this.showAlert(response.message, 'success');
                $('#deleteModal').modal('hide');

                // Reload the page after a short delay
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                this.showAlert(response.message, 'error');
            }
        } catch (error) {
            console.error('Delete error:', error);
            this.showAlert('Error deleting room. Please try again.', 'error');
        } finally {
            this.currentRoomId = null;
        }
    }

    async deleteAllRooms() {
        try {
            const response = await $.ajax({
                url: '/rooms',
                type: 'DELETE',
                data: {
                    _token: this.getCsrfToken()
                }
            });

            console.log('Delete all response:', response);

            if (response.success) {
                this.showAlert(response.message, 'success');
                $('#deleteAllModal').modal('hide');

                // Reload the page after a short delay
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                this.showAlert(response.message, 'error');
            }
        } catch (error) {
            console.error('Delete all error:', error);
            this.showAlert('Error deleting all rooms. Please try again.', 'error');
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

// Initialize room manager when document is ready
$(document).ready(function () {
    console.log('Document ready, initializing RoomManager...');
    window.roomManager = new RoomManager();
});
