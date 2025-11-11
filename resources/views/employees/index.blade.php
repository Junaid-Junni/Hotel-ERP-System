{{-- resources/views/employees/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Employee Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Employee Management</h3>
                        <div>
                            <a href="{{ route('employees.create') }}" class="btn btn-success">
                                <i class="fa fa-plus"></i> Add Employee
                            </a>
                            <button class="btn btn-danger" id="deleteAllBtn">
                                <i class="fa fa-trash"></i> Delete All
                            </button>
                            <a href="{{ route('employees.trash.index') }}" class="btn btn-secondary">
                                <i class="fa fa-trash-alt"></i> View Trash
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($employees->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="employeesTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Employee ID</th>
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Department</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Salary</th>
                                        <th>Status</th>
                                        <th>Hire Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employees as $index => $employee)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $employee->employee_id }}</td>
                                        <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                                        <td>{{ $employee->position }}</td>
                                        <td>{{ $employee->department }}</td>
                                        <td>{{ $employee->email }}</td>
                                        <td>{{ $employee->phone }}</td>
                                        <td>${{ number_format($employee->salary, 2) }}</td>
                                        <td>
                                            <span class="badge
                                                @if($employee->status == 'Active') bg-success
                                                @elseif($employee->status == 'Inactive') bg-secondary
                                                @elseif($employee->status == 'Suspended') bg-warning
                                                @elseif($employee->status == 'Terminated') bg-danger
                                                @else bg-secondary
                                                @endif">
                                                {{ $employee->status }}
                                            </span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($employee->hire_date)->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-info view-btn" data-id="{{ $employee->id }}">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-sm btn-warning">
                                                    <i class="fa fa-edit"></i>
                                                </a>

                                                @if($employee->status == 'Active')
                                                    <button class="btn btn-sm btn-secondary status-btn" data-id="{{ $employee->id }}" data-status="Inactive">
                                                        <i class="fa fa-pause"></i>
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-success status-btn" data-id="{{ $employee->id }}" data-status="Active">
                                                        <i class="fa fa-play"></i>
                                                    </button>
                                                @endif

                                                <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $employee->id }}">
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
                            <h5><i class="icon fa fa-info"></i> No Employees Found!</h5>
                            <p>There are no employees in the system yet. <a href="{{ route('employees.create') }}">Add the first employee</a>.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Employee Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewModalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this employee?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete All Modal -->
<div class="modal fade" id="deleteAllModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete All</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete ALL employees? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteAll">Delete All</button>
            </div>
        </div>
    </div>
</div>

<!-- Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Status</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to change this employee's status to <span id="statusText"></span>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmStatus">Update Status</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/employees.js') }}"></script>
@endpush
