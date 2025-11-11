<?php
// app/Http/Controllers/RoomController.php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;

class RoomController extends Controller
{
    // Index - Display room management page
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getRooms($request);
        }

        $rooms = Room::latest()->get();
        return view('rooms.index', compact('rooms'));
    }

    // Create - Show room creation form
    public function create()
    {
        return view('rooms.create');
    }

    // Store - Create new room
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'RoomNo' => 'required|integer|between:1,29|unique:rooms,RoomNo',
            'Floor' => 'required|string|in:1st Floor,2nd Floor,3rd Floor,4th Floor',
            'Type' => 'required|string|in:Standard,Deluxe,Suite',
            'Price' => 'required|numeric|min:0',
            'Capacity' => 'required|integer|in:1,2,3,4',
            'Status' => 'required|string|in:Available,Occupied,Maintenance,Cleaning',
            'Description' => 'nullable|string|max:500',
            'Images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $roomData = $request->only([
                'RoomNo',
                'Floor',
                'Type',
                'Price',
                'Capacity',
                'Status',
                'Description'
            ]);

            // Handle boolean fields
            $booleanFields = ['AC', 'TV', 'WiFi', 'Geyser', 'Balcony', 'Intercom', 'RoomService', 'Minibar'];
            foreach ($booleanFields as $field) {
                $roomData[$field] = $request->has($field);
            }

            // Handle image upload
            if ($request->hasFile('Images')) {
                $imagePaths = [];
                foreach ($request->file('Images') as $image) {
                    $path = $image->store('room-images', 'public');
                    $imagePaths[] = $path;
                }
                $roomData['Images'] = json_encode($imagePaths);
            }

            Room::create($roomData);

            return redirect()->route('rooms.index')
                ->with('success', 'Room created successfully!');
        } catch (\Exception $e) {
            Log::error('Room creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create room: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Show - Display single room
    public function show($id)
    {
        try {
            $room = Room::findOrFail($id);
            return response()->json([
                'success' => true,
                'room' => $room
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Room not found'
            ], 404);
        }
    }

    // Edit - Get room data for editing
    public function edit($id)
    {
        try {
            $room = Room::findOrFail($id);
            return view('rooms.edit', compact('room'));
        } catch (\Exception $e) {
            return redirect()->route('rooms.index')
                ->with('error', 'Room not found');
        }
    }

    // Update - Edit room
    public function update(Request $request, $id)
    {
        try {
            $room = Room::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'RoomNo' => 'required|integer|between:1,29|unique:rooms,RoomNo,' . $room->id,
                'Floor' => 'required|string|in:1st Floor,2nd Floor,3rd Floor,4th Floor',
                'Type' => 'required|string|in:Standard,Deluxe,Suite',
                'Price' => 'required|numeric|min:0',
                'Capacity' => 'required|integer|in:1,2,3,4',
                'Status' => 'required|string|in:Available,Occupied,Maintenance,Cleaning',
                'Description' => 'nullable|string|max:500',
                'Images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $roomData = $request->only([
                'RoomNo',
                'Floor',
                'Type',
                'Price',
                'Capacity',
                'Status',
                'Description'
            ]);

            // Handle boolean fields
            $booleanFields = ['AC', 'TV', 'WiFi', 'Geyser', 'Balcony', 'Intercom', 'RoomService', 'Minibar'];
            foreach ($booleanFields as $field) {
                $roomData[$field] = $request->has($field);
            }

            // Handle image upload
            if ($request->hasFile('Images')) {
                // Delete old images
                if ($room->Images) {
                    $oldImages = json_decode($room->Images, true);
                    foreach ($oldImages as $oldImage) {
                        Storage::disk('public')->delete($oldImage);
                    }
                }

                $imagePaths = [];
                foreach ($request->file('Images') as $image) {
                    $path = $image->store('room-images', 'public');
                    $imagePaths[] = $path;
                }
                $roomData['Images'] = json_encode($imagePaths);
            }

            $room->update($roomData);

            return redirect()->route('rooms.index')
                ->with('success', 'Room updated successfully!');
        } catch (\Exception $e) {
            Log::error('Room update failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update room: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Get rooms for DataTables
    public function getRooms(Request $request)
    {
        try {
            $data = Room::latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '
                    <div class="btn-group">
                        <button class="btn btn-sm btn-info view-btn" data-id="' . $row->id . '" title="View">
                            <i class="fa fa-eye"></i>
                        </button>
                        <a href="' . route('rooms.edit', $row->id) . '" class="btn btn-sm btn-warning edit-btn" title="Edit">
                            <i class="fa fa-edit"></i>
                        </a>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '" title="Delete">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>';
                    return $actionBtn;
                })
                ->editColumn('Status', function ($row) {
                    $badgeClass = 'badge ';
                    switch ($row->Status) {
                        case 'Available':
                            $badgeClass .= 'bg-success';
                            break;
                        case 'Occupied':
                            $badgeClass .= 'bg-danger';
                            break;
                        case 'Maintenance':
                            $badgeClass .= 'bg-warning';
                            break;
                        case 'Cleaning':
                            $badgeClass .= 'bg-info';
                            break;
                        default:
                            $badgeClass .= 'bg-secondary';
                    }
                    return '<span class="' . $badgeClass . '">' . $row->Status . '</span>';
                })
                ->editColumn('Price', function ($row) {
                    return '$' . number_format($row->Price, 2);
                })
                ->editColumn('Capacity', function ($row) {
                    return $row->Capacity . ' Person' . ($row->Capacity > 1 ? 's' : '');
                })
                ->editColumn('AC', function ($row) {
                    return $row->AC ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-danger"></i>';
                })
                ->editColumn('WiFi', function ($row) {
                    return $row->WiFi ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-danger"></i>';
                })
                ->rawColumns(['action', 'Status', 'AC', 'WiFi'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error in getRooms:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch rooms'], 500);
        }
    }

    // Destroy - Soft delete room
    public function destroy($id)
    {
        try {
            $room = Room::findOrFail($id);
            $room->delete();

            return response()->json([
                'success' => true,
                'message' => 'Room moved to trash successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Room deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete room: ' . $e->getMessage()
            ], 500);
        }
    }

    // Delete All - Soft delete all rooms
    public function deleteAll()
    {
        try {
            Room::query()->delete();

            return response()->json([
                'success' => true,
                'message' => 'All rooms moved to trash successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Delete all rooms failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete all rooms: ' . $e->getMessage()
            ], 500);
        }
    }

    // Trash - View trashed rooms
    public function trash()
    {
        $trashedRooms = Room::onlyTrashed()->get();
        return view('rooms.trash', compact('trashedRooms'));
    }

    // Restore - Restore from trash
    public function restore($id)
    {
        try {
            $room = Room::onlyTrashed()->findOrFail($id);
            $room->restore();

            return response()->json([
                'success' => true,
                'message' => 'Room restored successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore room: ' . $e->getMessage()
            ], 500);
        }
    }

    // Force Delete - Permanently delete
    public function forceDelete($id)
    {
        try {
            $room = Room::onlyTrashed()->findOrFail($id);

            // Delete images
            if ($room->Images) {
                $images = json_decode($room->Images, true);
                foreach ($images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            $room->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Room permanently deleted!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete room: ' . $e->getMessage()
            ], 500);
        }
    }
}
