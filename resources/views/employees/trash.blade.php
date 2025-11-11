{{-- resources/views/employees/trash.blade.php --}}
@extends('layouts.app')

@section('title', 'Trashed Employees')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Trashed Employees</h3>
                        <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back to Employees
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($trashedEmployees->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Employee ID</th>
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Department</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Deleted At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($trashedEmployees as $employee)
                                    <tr>
                                        <td>{{ $employee->employee_id }}</td>
                                        <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                                        <td>{{ $employee->position }}</td>
                                        <td>{{ $employee->department }}</td>
                                        <td>{{ $employee->email }}</td>
                                        <td>
                                            <span class="badge badge-{{ $employee->status == 'Active' ? 'success' : ($employee->status == 'Inactive' ? 'secondary' : ($employee->status == 'Suspended' ? 'warning' : 'danger')) }}">
                                                {{ $employee->status }}
                                            </span>
                                        </td>
                                        <td>{{ $employee->deleted_at->format('M d, Y h:i A') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-success restore-btn" data-id="{{ $employee->id }}">
                                                    <i class="fa fa-undo"></i> Restore
                                                </button>
                                                <button class="btn btn-sm btn-danger force-delete-btn" data-id="{{ $employee->id }}">
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
                            <h5><i class="icon fa fa-info"></i> No Trashed Employees!</h5>
                            <p>There are no employees in the trash bin.</p>
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
                <p>Are you sure you want to restore this employee?</p>
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
                <p>Are you sure you want to permanently delete this employee? This action cannot be undone.</p>
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
    let currentEmployeeId = null;

    // Restore employee
    $('.restore-btn').click(function() {
        currentEmployeeId = $(this).data('id');
        $('#restoreModal').modal('show');
    });

    $('#confirmRestore').click(function() {
        if (!currentEmployeeId) return;

        $.ajax({
            url: "{{ url('employees/trash') }}/" + currentEmployeeId + "/restore",
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

    // Force delete employee
    $('.force-delete-btn').click(function() {
        currentEmployeeId = $(this).data('id');
        $('#forceDeleteModal').modal('show');
    });

    $('#confirmForceDelete').click(function() {
        if (!currentEmployeeId) return;

        $.ajax({
            url: "{{ url('employees/trash') }}/" + currentEmployeeId + "/force",
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
