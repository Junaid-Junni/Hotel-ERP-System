{{-- resources/views/housekeeping/trash.blade.php --}}
@extends('layouts.app')

@section('title', 'Trashed Housekeeping Tasks')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Trashed Housekeeping Tasks</h3>
                        <a href="{{ route('housekeeping.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back to Tasks
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($trashedTasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Room</th>
                                        <th>Task Type</th>
                                        <th>Assigned To</th>
                                        <th>Scheduled Date</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Deleted At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($trashedTasks as $task)
                                    <tr>
                                        <td>{{ $task->room->RoomNo }} ({{ $task->room->Type }})</td>
                                        <td>{{ $task->task_type }}</td>
                                        <td>{{ $task->assignedEmployee->first_name }} {{ $task->assignedEmployee->last_name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($task->scheduled_date)->format('M d, Y h:i A') }}</td>
                                        <td>
                                            <span class="badge badge-{{ $task->status == 'Pending' ? 'warning' : ($task->status == 'In Progress' ? 'primary' : ($task->status == 'Completed' ? 'success' : 'secondary')) }}">
                                                {{ $task->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $task->priority == 'Low' ? 'success' : ($task->priority == 'Medium' ? 'info' : ($task->priority == 'High' ? 'warning' : 'danger')) }}">
                                                {{ $task->priority }}
                                            </span>
                                        </td>
                                        <td>{{ $task->deleted_at->format('M d, Y h:i A') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-success restore-btn" data-id="{{ $task->id }}">
                                                    <i class="fa fa-undo"></i> Restore
                                                </button>
                                                <button class="btn btn-sm btn-danger force-delete-btn" data-id="{{ $task->id }}">
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
                            <h5><i class="icon fa fa-info"></i> No Trashed Tasks!</h5>
                            <p>There are no tasks in the trash bin.</p>
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
                <p>Are you sure you want to restore this task?</p>
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
                <p>Are you sure you want to permanently delete this task? This action cannot be undone.</p>
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
    let currentTaskId = null;

    // Restore task
    $('.restore-btn').click(function() {
        currentTaskId = $(this).data('id');
        $('#restoreModal').modal('show');
    });

    $('#confirmRestore').click(function() {
        if (!currentTaskId) return;

        $.ajax({
            url: "{{ url('housekeeping/trash') }}/" + currentTaskId + "/restore",
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

    // Force delete task
    $('.force-delete-btn').click(function() {
        currentTaskId = $(this).data('id');
        $('#forceDeleteModal').modal('show');
    });

    $('#confirmForceDelete').click(function() {
        if (!currentTaskId) return;

        $.ajax({
            url: "{{ url('housekeeping/trash') }}/" + currentTaskId + "/force",
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
