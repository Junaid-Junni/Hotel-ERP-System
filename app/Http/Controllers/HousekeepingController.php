<?php

namespace App\Http\Controllers;

use App\Models\HousekeepingTask;
use App\Models\Room;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HousekeepingController extends Controller
{
    // Display all housekeeping tasks
    public function index()
    {
        $housekeepings = HousekeepingTask::with(['room', 'employee'])
            ->latest()
            ->get();

        return view('housekeeping.index', compact('housekeepings'));
    }

    // Show create form
    public function create()
    {
        $rooms = Room::whereIn('Status', ['Occupied', 'Available', 'Cleaning'])->get();
        $employees = Employee::where('department', 'Housekeeping')
            ->where('status', 'Active')
            ->get();

        $cleaningTasks = [
            'Make bed',
            'Vacuum carpet',
            'Clean bathroom',
            'Restock amenities',
            'Clean windows',
            'Dust furniture',
            'Empty trash',
            'Clean mirrors',
            'Mop floor',
            'Disinfect surfaces',
            'Check minibar',
            'Replace linens'
        ];

        return view('housekeeping.create', compact('rooms', 'employees', 'cleaningTasks'));
    }

    // Store new housekeeping task
    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'employee_id' => 'required|exists:employees,id',
            'cleaning_date' => 'required|date',
            'cleaning_time' => 'required',
            'cleaning_type' => 'required|in:Daily,Checkout,Deep,Maintenance',
            'tasks' => 'required|array',
            'tasks.*' => 'string',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'special_instructions' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:500'
        ]);

        $housekeeping = HousekeepingTask::create([
            'room_id' => $request->room_id,
            'employee_id' => $request->employee_id,
            'cleaning_date' => $request->cleaning_date,
            'cleaning_time' => $request->cleaning_time,
            'cleaning_type' => $request->cleaning_type,
            'tasks' => $request->tasks,
            'duration_minutes' => $request->duration_minutes,
            'special_instructions' => $request->special_instructions,
            'notes' => $request->notes,
            'status' => 'Scheduled'
        ]);

        // Update room status to Cleaning
        $room = Room::find($request->room_id);
        $room->update(['Status' => 'Cleaning']);

        return redirect()->route('housekeeping.index')
            ->with('success', 'Housekeeping task scheduled successfully!');
    }

    // Show single housekeeping task
    public function show($id)
    {
        $housekeeping = HousekeepingTask::with(['room', 'employee'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'housekeeping' => $housekeeping
        ]);
    }

    // Show edit form
    public function edit($id)
    {
        $housekeeping = HousekeepingTask::findOrFail($id);
        $rooms = Room::whereIn('Status', ['Occupied', 'Available', 'Cleaning'])->get();
        $employees = Employee::where('department', 'Housekeeping')
            ->where('status', 'Active')
            ->get();

        $cleaningTasks = [
            'Make bed',
            'Vacuum carpet',
            'Clean bathroom',
            'Restock amenities',
            'Clean windows',
            'Dust furniture',
            'Empty trash',
            'Clean mirrors',
            'Mop floor',
            'Disinfect surfaces',
            'Check minibar',
            'Replace linens'
        ];

        return view('housekeeping.edit', compact('housekeeping', 'rooms', 'employees', 'cleaningTasks'));
    }

    // Update housekeeping task
    public function update(Request $request, $id)
    {
        $housekeeping = HousekeepingTask::findOrFail($id);

        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'employee_id' => 'required|exists:employees,id',
            'cleaning_date' => 'required|date',
            'cleaning_time' => 'required',
            'cleaning_type' => 'required|in:Daily,Checkout,Deep,Maintenance',
            'tasks' => 'required|array',
            'tasks.*' => 'string',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'special_instructions' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:500'
        ]);

        $housekeeping->update([
            'room_id' => $request->room_id,
            'employee_id' => $request->employee_id,
            'cleaning_date' => $request->cleaning_date,
            'cleaning_time' => $request->cleaning_time,
            'cleaning_type' => $request->cleaning_type,
            'tasks' => $request->tasks,
            'duration_minutes' => $request->duration_minutes,
            'special_instructions' => $request->special_instructions,
            'notes' => $request->notes
        ]);

        return redirect()->route('housekeeping.index')
            ->with('success', 'Housekeeping task updated successfully!');
    }

    // Delete housekeeping task
    public function destroy($id)
    {
        $housekeeping = HousekeepingTask::findOrFail($id);

        // Update room status back to Available if task was in progress
        if ($housekeeping->status === 'In Progress') {
            $housekeeping->room->update(['Status' => 'Available']);
        }

        $housekeeping->delete();

        return response()->json([
            'success' => true,
            'message' => 'Housekeeping task deleted successfully!'
        ]);
    }

    // Mark task as in progress
    public function markInProgress($id)
    {
        $housekeeping = HousekeepingTask::findOrFail($id);
        $housekeeping->markInProgress();

        return response()->json([
            'success' => true,
            'message' => 'Task marked as In Progress!'
        ]);
    }

    // Mark task as completed
    public function markCompleted(Request $request, $id)
    {
        $housekeeping = HousekeepingTask::findOrFail($id);

        $request->validate([
            'quality_rating' => 'required|integer|min:1|max:5',
            'supervisor_notes' => 'nullable|string|max:500',
            'issues_found' => 'nullable|string|max:500',
            'cleaning_supplies_cost' => 'nullable|numeric|min:0'
        ]);

        $housekeeping->markCompleted(
            $request->quality_rating,
            $request->supervisor_notes
        );

        $housekeeping->update([
            'issues_found' => $request->issues_found,
            'cleaning_supplies_cost' => $request->cleaning_supplies_cost
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task marked as Completed!'
        ]);
    }

    // Cancel housekeeping task
    public function cancel($id)
    {
        $housekeeping = HousekeepingTask::findOrFail($id);

        $housekeeping->update([
            'status' => 'Cancelled'
        ]);

        // Update room status back to Available
        $housekeeping->room->update(['Status' => 'Available']);

        return response()->json([
            'success' => true,
            'message' => 'Housekeeping task cancelled!'
        ]);
    }

    // Dashboard view
    public function dashboard()
    {
        $todayTasks = Housekeeping::with(['room', 'employee'])
            ->where('cleaning_date', today())
            ->orderBy('cleaning_time')
            ->get();

        $scheduledCount = Housekeeping::scheduled()->count();
        $inProgressCount = Housekeeping::inProgress()->count();
        $completedToday = Housekeeping::completed()->where('cleaning_date', today())->count();
        $overdueCount = Housekeeping::scheduled()->where('cleaning_date', '<', today())->count();

        $employees = Employee::where('department', 'Housekeeping')
            ->where('status', 'Active')
            ->withCount(['housekeepings as today_tasks_count' => function ($query) {
                $query->where('cleaning_date', today());
            }])
            ->get();

        return view('housekeeping.dashboard', compact(
            'todayTasks',
            'scheduledCount',
            'inProgressCount',
            'completedToday',
            'overdueCount',
            'employees'
        ));
    }

    // Get tasks by date (for calendar)
    public function getTasksByDate($date)
    {
        $tasks = HousekeepingTask::with(['room', 'employee'])
            ->where('cleaning_date', $date)
            ->orderBy('cleaning_time')
            ->get();

        return response()->json([
            'success' => true,
            'tasks' => $tasks
        ]);
    }

    // Trash index
    public function trashIndex()
    {
        $trashedHousekeepings = HousekeepingTask::onlyTrashed()
            ->with(['room', 'employee'])
            ->latest()
            ->get();

        return view('housekeeping.trash', compact('trashedHousekeepings'));
    }

    // Restore from trash
    public function trashRestore($id)
    {
        $housekeeping = HousekeepingTask::onlyTrashed()->findOrFail($id);
        $housekeeping->restore();

        return response()->json([
            'success' => true,
            'message' => 'Housekeeping task restored successfully!'
        ]);
    }

    // Permanently delete from trash
    public function trashDestroy($id)
    {
        $housekeeping = HousekeepingTask::onlyTrashed()->findOrFail($id);
        $housekeeping->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Housekeeping task permanently deleted!'
        ]);
    }

    // Empty trash
    public function trashEmpty()
    {
        HousekeepingTask::onlyTrashed()->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Trash emptied successfully!'
        ]);
    }
}
