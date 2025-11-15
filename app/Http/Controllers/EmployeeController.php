<?php
// app/Http/Controllers/EmployeeController.php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    // Index - Display employee management page
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getEmployees($request);
        }

        $employees = Employee::latest()->get();
        return view('employees.index', compact('employees'));
    }

    // Create - Show employee creation form
    public function create()
    {
        $employeeId = Employee::generateEmployeeId();
        return view('employees.create', compact('employeeId'));
    }

    // Get employees for DataTables
    public function getEmployees(Request $request)
    {
        try {
            $data = Employee::latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '
                    <div class="btn-group">
                        <button class="btn btn-sm btn-info view-btn" data-id="' . $row->id . '" title="View">
                            <i class="fa fa-eye"></i>
                        </button>
                        <a href="' . route('employees.edit', $row->id) . '" class="btn btn-sm btn-warning edit-btn" title="Edit">
                            <i class="fa fa-edit"></i>
                        </a>';

                    // Status change button
                    if ($row->status == 'Active') {
                        $actionBtn .= '
                        <button class="btn btn-sm btn-secondary status-btn" data-id="' . $row->id . '" data-status="Inactive" title="Deactivate">
                            <i class="fa fa-pause"></i>
                        </button>';
                    } else {
                        $actionBtn .= '
                        <button class="btn btn-sm btn-success status-btn" data-id="' . $row->id . '" data-status="Active" title="Activate">
                            <i class="fa fa-play"></i>
                        </button>';
                    }

                    $actionBtn .= '
                        <button class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '" title="Delete">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>';
                    return $actionBtn;
                })
                ->editColumn('status', function ($row) {
                    $badgeClass = 'badge ';
                    switch ($row->status) {
                        case 'Active':
                            $badgeClass .= 'bg-success';
                            break;
                        case 'Inactive':
                            $badgeClass .= 'bg-secondary';
                            break;
                        case 'Suspended':
                            $badgeClass .= 'bg-warning';
                            break;
                        case 'Terminated':
                            $badgeClass .= 'bg-danger';
                            break;
                        default:
                            $badgeClass .= 'bg-secondary';
                    }
                    return '<span class="' . $badgeClass . '">' . $row->status . '</span>';
                })
                ->editColumn('full_name', function ($row) {
                    return $row->first_name . ' ' . $row->last_name;
                })
                ->editColumn('salary', function ($row) {
                    return '$' . number_format($row->salary, 2);
                })
                ->editColumn('hire_date', function ($row) {
                    return Carbon::parse($row->hire_date)->format('M d, Y');
                })
                ->editColumn('date_of_birth', function ($row) {
                    return Carbon::parse($row->date_of_birth)->format('M d, Y');
                })
                ->addColumn('employment_info', function ($row) {
                    return $row->position . ' (' . $row->department . ')';
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error in getEmployees:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch employees'], 500);
        }
    }

    // Show - Display single employee
    public function show($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            return view('employees.show', compact('employee'));
            // return response()->json([
            //     'success' => true,
            //     'employee' => $employee
            // ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ], 404);
        }
    }

    // Edit - Get employee data for editing
    public function edit($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            return view('employees.edit', compact('employee'));
        } catch (\Exception $e) {
            return redirect()->route('employees.index')
                ->with('error', 'Employee not found');
        }
    }

    // Store - Create new employee
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|string|max:20|unique:employees,employee_id',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date|before:-18 years',
            'gender' => 'required|in:Male,Female,Other',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'position' => 'required|string|max:100',
            'department' => 'required|string|max:100',
            'salary' => 'required|numeric|min:0',
            'hire_date' => 'required|date',
            'employment_type' => 'required|in:Full Time,Part Time,Contract,Temporary',
            'status' => 'required|in:Active,Inactive,Suspended,Terminated',
            'emergency_contact_name' => 'required|string|max:100',
            'emergency_contact_phone' => 'required|string|max:20',
            'emergency_contact_relation' => 'required|string|max:100',
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:50',
            'routing_number' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:1000',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $employeeData = $request->except('profile_image');

            // Handle profile image upload
            if ($request->hasFile('profile_image')) {
                $imagePath = $request->file('profile_image')->store('employee-profiles', 'public');
                $employeeData['profile_image'] = $imagePath;
            }

            Employee::create($employeeData);

            return redirect()->route('employees.index')
                ->with('success', 'Employee created successfully!');
        } catch (\Exception $e) {
            Log::error('Employee creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create employee: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Update - Edit employee
    public function update(Request $request, $id)
    {
        try {
            $employee = Employee::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'employee_id' => 'required|string|max:20|unique:employees,employee_id,' . $employee->id,
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'email' => 'required|email|unique:employees,email,' . $employee->id,
                'phone' => 'required|string|max:20',
                'date_of_birth' => 'required|date|before:-18 years',
                'gender' => 'required|in:Male,Female,Other',
                'address' => 'required|string|max:500',
                'city' => 'required|string|max:100',
                'state' => 'required|string|max:100',
                'zip_code' => 'required|string|max:20',
                'country' => 'required|string|max:100',
                'position' => 'required|string|max:100',
                'department' => 'required|string|max:100',
                'salary' => 'required|numeric|min:0',
                'hire_date' => 'required|date',
                'employment_type' => 'required|in:Full Time,Part Time,Contract,Temporary',
                'status' => 'required|in:Active,Inactive,Suspended,Terminated',
                'emergency_contact_name' => 'required|string|max:100',
                'emergency_contact_phone' => 'required|string|max:20',
                'emergency_contact_relation' => 'required|string|max:100',
                'bank_name' => 'nullable|string|max:100',
                'account_number' => 'nullable|string|max:50',
                'routing_number' => 'nullable|string|max:20',
                'notes' => 'nullable|string|max:1000',
                'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $employeeData = $request->except('profile_image');

            // Handle profile image upload
            if ($request->hasFile('profile_image')) {
                // Delete old image if exists
                if ($employee->profile_image) {
                    Storage::disk('public')->delete($employee->profile_image);
                }

                $imagePath = $request->file('profile_image')->store('employee-profiles', 'public');
                $employeeData['profile_image'] = $imagePath;
            }

            $employee->update($employeeData);

            return redirect()->route('employees.index')
                ->with('success', 'Employee updated successfully!');
        } catch (\Exception $e) {
            Log::error('Employee update failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update employee: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Update Status
    public function updateStatus(Request $request, $id)
    {
        try {
            $employee = Employee::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'status' => 'required|in:Active,Inactive,Suspended,Terminated'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status'
                ], 422);
            }

            $employee->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => 'Employee status updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    // Destroy - Soft delete employee
    public function destroy($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $employee->delete();

            return redirect()->route('employees.index')->with('success', 'Employee moved to trash successfully!');
            // return response()->json([
            //     'success' => true,
            //     'message' => 'Employee moved to trash successfully!'
            // ]);
        } catch (\Exception $e) {
            Log::error('Employee deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete employee: ' . $e->getMessage()
            ], 500);
        }
    }

    // Delete All - Soft delete all employees
    public function deleteAll()
    {
        try {
            Employee::query()->delete();

            return response()->json([
                'success' => true,
                'message' => 'All employees moved to trash successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Delete all employees failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete all employees: ' . $e->getMessage()
            ], 500);
        }
    }

    // Trash - View trashed employees
    public function trash()
    {
        $trashedEmployees = Employee::onlyTrashed()->get();
        return view('employees.trash', compact('trashedEmployees'));
    }

    // Restore - Restore from trash
    public function restore($id)
    {
        try {
            $employee = Employee::onlyTrashed()->findOrFail($id);
            $employee->restore();

            return response()->json([
                'success' => true,
                'message' => 'Employee restored successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore employee: ' . $e->getMessage()
            ], 500);
        }
    }

    // Force Delete - Permanently delete
    public function forceDelete($id)
    {
        try {
            $employee = Employee::onlyTrashed()->findOrFail($id);

            // Delete profile image if exists
            if ($employee->profile_image) {
                Storage::disk('public')->delete($employee->profile_image);
            }

            $employee->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Employee permanently deleted!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete employee: ' . $e->getMessage()
            ], 500);
        }
    }
}
