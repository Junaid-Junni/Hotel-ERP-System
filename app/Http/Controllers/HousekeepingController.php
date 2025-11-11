<?php
// app/Http/Controllers/HousekeepingController.php

namespace App\Http\Controllers;

use App\Models\HousekeepingTask;
use App\Models\Room;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HousekeepingController extends Controller
{
    // Index - Display housekeeping management page
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getTasks($request);
        }

        $tasks = HousekeepingTask::with(['room', 'assignedEmployee'])->latest()->get();
        return view('housekeeping.index', compact('tasks'));
    }

    // Create - Show task creation form
    public function create()
    {
        $rooms = Room::where('Status', '!=', 'Maintenance')->get();
        $employees = Employee::where('status', 'Active')
            ->where('department', 'Housekeeping')
            ->get();
        return view('housekeeping.create', compact('rooms', 'employees'));
    }

    // Get tasks for DataTables
    public function getTasks(Request $request)
    {
        try {
            $data = HousekeepingTask::with(['room', 'assignedEmployee'])->latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '
                    <div class="btn-group">
                        <button class="btn btn-sm btn-info view-btn" data-id="' . $row->id . '" title="View">
                            <i class="fa fa-eye"></i>
                        </button>
                        <a href="' . route('housekeeping.edit', $row->id) . '" class="btn btn-sm btn-warning edit-btn" title="Edit">
                            <i class="fa fa-edit"></i>
                        </a>';

                    // Additional actions based on status
                    if ($row->status == 'Pending') {
                        $actionBtn .= '
                        <button class="btn btn-sm btn-success start-btn" data-id="' . $row->id . '" title="Start Task">
                            <i class="fa fa-play"></i>
                        </button>
                        <button class="btn btn-sm btn-danger cancel-btn" data-id="' . $row->id . '" title="Cancel Task">
                            <i class="fa fa-times"></i>
                        </button>';
                    } elseif ($row->status == 'In Progress') {
                        $actionBtn .= '
                        <button class="btn btn-sm btn-primary complete-btn" data-id="' . $row->id . '" title="Complete Task">
                            <i class="fa fa-check"></i>
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
                        case 'Pending':
                            $badgeClass .= $row->is_overdue ? 'bg-danger' : 'bg-warning';
                            break;
                        case 'In Progress':
                            $badgeClass .= 'bg-primary';
                            break;
                        case 'Completed':
                            $badgeClass .= 'bg-success';
                            break;
                        case 'Cancelled':
                            $badgeClass .= 'bg-secondary';
                            break;
                        default:
                            $badgeClass .= 'bg-secondary';
                    }
                    $overdueBadge = $row->is_overdue ? ' <span class="badge bg-danger">Overdue</span>' : '';
                    return '<span class="' . $badgeClass . '">' . $row->status . '</span>' . $overdueBadge;
                })
                ->editColumn('priority', function ($row) {
                    $badgeClass = 'badge ';
                    switch ($row->priority) {
                        case 'Low':
                            $badgeClass .= 'bg-success';
                            break;
                        case 'Medium':
                            $badgeClass .= 'bg-info';
                            break;
                        case 'High':
                            $badgeClass .= 'bg-warning';
                            break;
                        case 'Urgent':
                            $badgeClass .= 'bg-danger';
                            break;
                        default:
                            $badgeClass .= 'bg-secondary';
                    }
                    return '<span class="' . $badgeClass . '">' . $row->priority . '</span>';
                })
                ->editColumn('room.RoomNo', function ($row) {
                    return $row->room->RoomNo . ' (' . $row->room->Type . ')';
                })
                ->editColumn('assignedEmployee.first_name', function ($row) {
                    return $row->assignedEmployee->first_name . ' ' . $row->assignedEmployee->last_name;
                })
                ->editColumn('scheduled_date', function ($row) {
                    return Carbon::parse($row->scheduled_date)->format('M d, Y h:i A');
                })
                ->editColumn('estimated_minutes', function ($row) {
                    return $row->estimated_minutes ? $row->estimated_minutes . ' mins' : 'N/A';
                })
                ->editColumn('actual_minutes', function ($row) {
                    return $row->actual_minutes ? $row->actual_minutes . ' mins' : 'N/A';
                })
                ->rawColumns(['action', 'status', 'priority'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error in getTasks:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch tasks'], 500);
        }
    }

    // Show - Display single task
    public function show($id)
    {
        try {
            $task = HousekeepingTask::with(['room', 'assignedEmployee'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'task' => $task
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found'
            ], 404);
        }
    }

    // Edit - Get task data for editing
    public function edit($id)
    {
        try {
            $task = HousekeepingTask::with(['room', 'assignedEmployee'])->findOrFail($id);
            $rooms = Room::where('Status', '!=', 'Maintenance')->get();
            $employees = Employee::where('status', 'Active')
                ->where('department', 'Housekeeping')
                ->get();
            return view('housekeeping.edit', compact('task', 'rooms', 'employees'));
        } catch (\Exception $e) {
            return redirect()->route('housekeeping.index')
                ->with('error', 'Task not found');
        }
    }

    // Store - Create new task
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'assigned_to' => 'required|exists:employees,id',
            'task_type' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'priority' => 'required|in:Low,Medium,High,Urgent',
            'scheduled_date' => 'required|date',
            'estimated_minutes' => 'nullable|integer|min:1',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $taskData = $request->only([
                'room_id',
                'assigned_to',
                'task_type',
                'description',
                'priority',
                'scheduled_date',
                'estimated_minutes',
                'notes'
            ]);

            // Set scheduled date with time if provided
            if ($request->has('scheduled_time')) {
                $taskData['scheduled_date'] = $request->scheduled_date . ' ' . $request->scheduled_time;
            }

            HousekeepingTask::create($taskData);

            return redirect()->route('housekeeping.index')
                ->with('success', 'Housekeeping task created successfully!');
        } catch (\Exception $e) {
            Log::error('Task creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create task: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Update - Edit task
    public function update(Request $request, $id)
    {
        try {
            $task = HousekeepingTask::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'room_id' => 'required|exists:rooms,id',
                'assigned_to' => 'required|exists:employees,id',
                'task_type' => 'required|string|max:100',
                'description' => 'nullable|string|max:500',
                'priority' => 'required|in:Low,Medium,High,Urgent',
                'scheduled_date' => 'required|date',
                'estimated_minutes' => 'nullable|integer|min:1',
                'notes' => 'nullable|string|max:1000'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $taskData = $request->only([
                'room_id',
                'assigned_to',
                'task_type',
                'description',
                'priority',
                'scheduled_date',
                'estimated_minutes',
                'notes'
            ]);

            // Set scheduled date with time if provided
            if ($request->has('scheduled_time')) {
                $taskData['scheduled_date'] = $request->scheduled_date . ' ' . $request->scheduled_time;
            }

            $task->update($taskData);

            return redirect()->route('housekeeping.index')
                ->with('success', 'Housekeeping task updated successfully!');
        } catch (\Exception $e) {
            Log::error('Task update failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update task: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Start Task
    public function startTask($id)
    {
        try {
            $task = HousekeepingTask::findOrFail($id);

            if ($task->status != 'Pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending tasks can be started.'
                ]);
            }

            $task->update([
                'status' => 'In Progress',
                'started_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task started successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to start task: ' . $e->getMessage()
            ], 500);
        }
    }

    // Complete Task
    public function completeTask(Request $request, $id)
    {
        try {
            $task = HousekeepingTask::findOrFail($id);

            if ($task->status != 'In Progress') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only in-progress tasks can be completed.'
                ]);
            }

            $validator = Validator::make($request->all(), [
                'actual_minutes' => 'required|integer|min:1',
                'completion_notes' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed!',
                    'errors' => $validator->errors()
                ], 422);
            }

            $task->update([
                'status' => 'Completed',
                'completed_at' => now(),
                'actual_minutes' => $request->actual_minutes,
                'notes' => $request->completion_notes ?: $task->notes
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task completed successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete task: ' . $e->getMessage()
            ], 500);
        }
    }

    // Cancel Task
    public function cancelTask(Request $request, $id)
    {
        try {
            $task = HousekeepingTask::findOrFail($id);

            if (!in_array($task->status, ['Pending', 'In Progress'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending or in-progress tasks can be cancelled.'
                ]);
            }

            $task->update([
                'status' => 'Cancelled',
                'cancellation_reason' => $request->reason
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task cancelled successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel task: ' . $e->getMessage()
            ], 500);
        }
    }

    public function dashboard()
    {
        try {
            $totalTasks = HousekeepingTask::count();
            $pendingTasks = HousekeepingTask::where('status', 'Pending')->count();
            $inProgressTasks = HousekeepingTask::where('status', 'In Progress')->count();
            $completedTasks = HousekeepingTask::where('status', 'Completed')->count();

            $todayTasks = HousekeepingTask::whereDate('scheduled_date', today())->count();
            $overdueTasks = HousekeepingTask::where('status', 'Pending')
                ->where('scheduled_date', '<', now())
                ->count();

            $recentTasks = HousekeepingTask::with(['room', 'assignedEmployee'])
                ->latest()
                ->take(10)
                ->get();

            // Get tasks by priority for chart
            $priorityStats = HousekeepingTask::select('priority', DB::raw('count(*) as count'))
                ->groupBy('priority')
                ->get()
                ->pluck('count', 'priority')
                ->toArray();

            // Calculate efficiency
            $completedWithTime = HousekeepingTask::where('status', 'Completed')
                ->whereNotNull('actual_minutes')
                ->whereNotNull('estimated_minutes')
                ->count();

            $efficientTasks = HousekeepingTask::where('status', 'Completed')
                ->whereNotNull('actual_minutes')
                ->whereNotNull('estimated_minutes')
                ->whereRaw('actual_minutes <= estimated_minutes')
                ->count();

            $efficiency = $completedWithTime > 0 ? round(($efficientTasks / $completedWithTime) * 100, 1) : 0;

            // Get top performer
            $topPerformer = Employee::where('department', 'Housekeeping')
                ->where('status', 'Active')
                ->withCount(['housekeepingTasks as completed_tasks' => function ($query) {
                    $query->where('status', 'Completed');
                }])
                ->orderBy('completed_tasks', 'desc')
                ->first();

            // Get today's completed tasks
            $todayCompleted = HousekeepingTask::where('status', 'Completed')
                ->whereDate('completed_at', today())
                ->count();

            // Get available rooms count
            $availableRooms = Room::where('Status', 'Available')->count();

            // Get active housekeeping staff count
            $activeStaff = Employee::where('department', 'Housekeeping')
                ->where('status', 'Active')
                ->count();

            return view('housekeeping.dashboard', compact(
                'totalTasks',
                'pendingTasks',
                'inProgressTasks',
                'completedTasks',
                'todayTasks',
                'overdueTasks',
                'recentTasks',
                'priorityStats',
                'efficiency',
                'topPerformer',
                'todayCompleted',
                'availableRooms',
                'activeStaff'
            ));
        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage());

            // Return empty dashboard on error
            return view('housekeeping.dashboard', [
                'totalTasks' => 0,
                'pendingTasks' => 0,
                'inProgressTasks' => 0,
                'completedTasks' => 0,
                'todayTasks' => 0,
                'overdueTasks' => 0,
                'recentTasks' => collect(),
                'priorityStats' => [],
                'efficiency' => 0,
                'topPerformer' => null,
                'todayCompleted' => 0,
                'availableRooms' => 0,
                'activeStaff' => 0
            ])->with('error', 'Unable to load dashboard data: ' . $e->getMessage());
        }
    }
    // Calendar View
    public function calendar()
    {
        $tasks = HousekeepingTask::with(['room', 'assignedEmployee'])
            ->whereDate('scheduled_date', '>=', today())
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => 'Room ' . $task->room->RoomNo . ' - ' . $task->task_type,
                    'start' => $task->scheduled_date,
                    'end' => $task->scheduled_date->addMinutes($task->estimated_minutes ?: 60),
                    'color' => $this->getTaskColor($task->priority),
                    'extendedProps' => [
                        'assigned_to' => $task->assignedEmployee->first_name . ' ' . $task->assignedEmployee->last_name,
                        'status' => $task->status,
                        'priority' => $task->priority
                    ]
                ];
            });

        return view('housekeeping.calendar', compact('tasks'));
    }

    private function getTaskColor($priority)
    {
        switch ($priority) {
            case 'Urgent':
                return '#dc3545';
            case 'High':
                return '#fd7e14';
            case 'Medium':
                return '#ffc107';
            case 'Low':
                return '#198754';
            default:
                return '#6c757d';
        }
    }

    // Destroy - Soft delete task
    public function destroy($id)
    {
        try {
            $task = HousekeepingTask::findOrFail($id);
            $task->delete();

            return response()->json([
                'success' => true,
                'message' => 'Task moved to trash successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Task deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete task: ' . $e->getMessage()
            ], 500);
        }
    }

    // Delete All - Soft delete all tasks
    public function deleteAll()
    {
        try {
            HousekeepingTask::query()->delete();

            return response()->json([
                'success' => true,
                'message' => 'All tasks moved to trash successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Delete all tasks failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete all tasks: ' . $e->getMessage()
            ], 500);
        }
    }

    // Trash - View trashed tasks
    public function trash()
    {
        $trashedTasks = HousekeepingTask::onlyTrashed()
            ->with(['room', 'assignedEmployee'])
            ->get();
        return view('housekeeping.trash', compact('trashedTasks'));
    }

    // Restore - Restore from trash
    public function restore($id)
    {
        try {
            $task = HousekeepingTask::onlyTrashed()->findOrFail($id);
            $task->restore();

            return response()->json([
                'success' => true,
                'message' => 'Task restored successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore task: ' . $e->getMessage()
            ], 500);
        }
    }

    // Force Delete - Permanently delete
    public function forceDelete($id)
    {
        try {
            $task = HousekeepingTask::onlyTrashed()->findOrFail($id);
            $task->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Task permanently deleted!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete task: ' . $e->getMessage()
            ], 500);
        }
    }
}
