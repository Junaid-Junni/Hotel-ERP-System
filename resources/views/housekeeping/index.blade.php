@extends('layouts.app')

@section('title', 'Housekeeping Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-broom"></i> Housekeeping Management
                        </h3>
                        <div class="btn-group">
                            <a href="{{ route('housekeeping.dashboard') }}" class="btn btn-light">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                            <a href="{{ route('housekeeping.create') }}" class="btn btn-success">
                                <i class="fas fa-plus"></i> Schedule Cleaning
                            </a>
                            <a href="{{ route('housekeeping.trash.index') }}" class="btn btn-secondary">
                                <i class="fas fa-trash-alt"></i> View Trash
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if($housekeepings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Room</th>
                                        <th>Housekeeper</th>
                                        <th>Date & Time</th>
                                        <th>Type</th>
                                        <th>Duration</th>
                                        <th>Status</th>
                                        <th>Tasks</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($housekeepings as $index => $hk)
                                    <tr class="@if($hk->isOverdue()) table-danger @endif">
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $hk->room->RoomNo }}</strong>
                                            <br><small class="text-muted">{{ $hk->room->Type }}</small>
                                        </td>
                                        <td>
                                            {{ $hk->employee->first_name }} {{ $hk->employee->last_name }}
                                            <br><small class="text-muted">{{ $hk->employee->employee_id }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $hk->formatted_cleaning_date }}</strong>
                                            <br><small>{{ $hk->formatted_cleaning_time }}</small>
                                            @if($hk->isOverdue())
                                                <br><span class="badge badge-danger">Overdue</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $hk->cleaning_type_badge }}">
                                                {{ $hk->cleaning_type }}
                                            </span>
                                        </td>
                                        <td>{{ $hk->duration_formatted }}</td>
                                        <td>
                                            <span class="badge badge-{{ $hk->status_badge }}">
                                                {{ $hk->status }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($hk->tasks && count($hk->tasks) > 0)
                                                <small>
                                                    {{ implode(', ', array_slice($hk->tasks, 0, 2)) }}
                                                    @if(count($hk->tasks) > 2)
                                                        ... (+{{ count($hk->tasks) - 2 }} more)
                                                    @endif
                                                </small>
                                            @else
                                                <small class="text-muted">No tasks</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-info view-btn" data-id="{{ $hk->id }}" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <a href="{{ route('housekeeping.edit', $hk->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                @if($hk->status == 'Scheduled')
                                                    <button class="btn btn-sm btn-success progress-btn" data-id="{{ $hk->id }}" title="Mark In Progress">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger cancel-btn" data-id="{{ $hk->id }}" title="Cancel">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @elseif($hk->status == 'In Progress')
                                                    <button class="btn btn-sm btn-primary complete-btn" data-id="{{ $hk->id }}" title="Mark Complete">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif

                                                <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $hk->id }}" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <h5><i class="fas fa-info-circle"></i> No Housekeeping Tasks Found!</h5>
                            <p>There are no housekeeping tasks scheduled yet. <a href="{{ route('housekeeping.create') }}">Schedule the first cleaning task</a>.</p>
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
                <h5 class="modal-title">Housekeeping Task Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewModalBody">
                <!-- Content will be loaded here -->
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
                <p>Are you sure you want to delete this housekeeping task?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- In Progress Modal -->
<div class="modal fade" id="progressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Start Cleaning</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to mark this task as In Progress?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmProgress">Start Cleaning</button>
            </div>
        </div>
    </div>
</div>

<!-- Complete Modal -->
<div class="modal fade" id="completeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Complete Cleaning Task</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="completeForm">
                    <div class="form-group">
                        <label for="quality_rating">Quality Rating *</label>
                        <select class="form-control" id="quality_rating" name="quality_rating" required>
                            <option value="">Select Rating</option>
                            <option value="1">1 - Poor</option>
                            <option value="2">2 - Fair</option>
                            <option value="3">3 - Good</option>
                            <option value="4">4 - Very Good</option>
                            <option value="5">5 - Excellent</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="cleaning_supplies_cost">Cleaning Supplies Cost ($)</label>
                        <input type="number" step="0.01" class="form-control" id="cleaning_supplies_cost" name="cleaning_supplies_cost" value="0">
                    </div>
                    <div class="form-group">
                        <label for="issues_found">Issues Found</label>
                        <textarea class="form-control" id="issues_found" name="issues_found" rows="2" placeholder="Any issues found during cleaning..."></textarea>
                    </div>
                    <div class="form-group">
                        <label for="supervisor_notes">Supervisor Notes</label>
                        <textarea class="form-control" id="supervisor_notes" name="supervisor_notes" rows="2" placeholder="Supervisor comments..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmComplete">Mark Complete</button>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Cancellation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel this housekeeping task?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmCancel">Cancel Task</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/housekeeping.js') }}"></script>
@endpush
