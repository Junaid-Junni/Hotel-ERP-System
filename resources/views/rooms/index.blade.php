{{-- resources/views/rooms/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Room Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Room Management</h3>
                        <div>
                            <a href="{{ route('rooms.create') }}" class="btn btn-success">
                                <i class="fa fa-plus"></i> Add New Room
                            </a>
                            <button class="btn btn-danger" id="deleteAllBtn">
                                <i class="fa fa-trash"></i> Delete All
                            </button>
                            <a href="{{ route('rooms.trash.index') }}" class="btn btn-secondary">
                                <i class="fa fa-trash-alt"></i> View Trash
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if($rooms->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="roomsTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Room No</th>
                                        <th>Floor</th>
                                        <th>Type</th>
                                        <th>Price</th>
                                        <th>Capacity</th>
                                        <th>Status</th>
                                        <th>AC</th>
                                        <th>WiFi</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rooms as $index => $room)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $room->RoomNo }}</td>
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
                                            @if($room->AC)
                                                <i class="fa fa-check text-success"></i>
                                            @else
                                                <i class="fa fa-times text-danger"></i>
                                            @endif
                                        </td>
                                        <td>
                                            @if($room->WiFi)
                                                <i class="fa fa-check text-success"></i>
                                            @else
                                                <i class="fa fa-times text-danger"></i>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-info view-btn" data-id="{{ $room->id }}" title="View">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                <a href="{{ route('rooms.edit', $room->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $room->id }}" title="Delete">
                                                    <i class="fa fa-trash"></i>
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
                            <h5><i class="icon fa fa-info"></i> No Rooms Found!</h5>
                            <p>There are no rooms in the system yet. <a href="{{ route('rooms.create') }}">Create the first room</a>.</p>
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

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this room? This action can be undone from trash.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete All Modal -->
<div class="modal fade" id="deleteAllModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete All</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete ALL rooms? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteAll">Delete All</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/rooms.js') }}"></script>
@endpush
