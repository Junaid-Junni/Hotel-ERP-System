<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::all();
        return view('rooms.index', compact('rooms'));
    }

    public function create()
    {
        return view('rooms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'RoomNo' => 'required|string|unique:rooms,RoomNo',
            'Floor' => 'required|integer|min:1',
            'Type' => 'required|string|max:255',
            'Price' => 'required|numeric|min:0',
            'Capacity' => 'required|integer|min:1',
            'Status' => 'required|in:Available,Occupied,Maintenance,Cleaning',
            'Description' => 'nullable|string',
            'AC' => 'boolean',
            'WiFi' => 'boolean',
            'TV' => 'boolean',
            'Geyser' => 'boolean',
            'Balcony' => 'boolean',
            'Intercom' => 'boolean',
            'RoomService' => 'boolean',
            'Minibar' => 'boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Handle boolean fields
        $booleanFields = ['AC', 'WiFi', 'TV', 'Geyser', 'Balcony', 'Intercom', 'RoomService', 'Minibar'];
        foreach ($booleanFields as $field) {
            $validated[$field] = $request->has($field);
        }

        // Handle image uploads
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('room-images', 'public');
                $imagePaths[] = $path;
            }
            $validated['Images'] = $imagePaths;
        }

        Room::create($validated);

        return redirect()->route('rooms.index')
            ->with('success', 'Room created successfully!');
    }

    public function show($id)
    {
        $room = Room::findOrFail($id);

        return response()->json([
            'success' => true,
            'room' => $room
        ]);
    }

    public function edit($id)
    {
        $room = Room::findOrFail($id);
        return view('rooms.edit', compact('room'));
    }

    public function update(Request $request, $id)
    {
        $room = Room::findOrFail($id);

        $validated = $request->validate([
            'RoomNo' => 'required|string|unique:rooms,RoomNo,' . $id,
            'Floor' => 'required|integer|min:1',
            'Type' => 'required|string|max:255',
            'Price' => 'required|numeric|min:0',
            'Capacity' => 'required|integer|min:1',
            'Status' => 'required|in:Available,Occupied,Maintenance,Cleaning',
            'Description' => 'nullable|string',
            'AC' => 'boolean',
            'WiFi' => 'boolean',
            'TV' => 'boolean',
            'Geyser' => 'boolean',
            'Balcony' => 'boolean',
            'Intercom' => 'boolean',
            'RoomService' => 'boolean',
            'Minibar' => 'boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Handle boolean fields
        $booleanFields = ['AC', 'WiFi', 'TV', 'Geyser', 'Balcony', 'Intercom', 'RoomService', 'Minibar'];
        foreach ($booleanFields as $field) {
            $validated[$field] = $request->has($field);
        }

        // Handle image uploads
        if ($request->hasFile('images')) {
            $imagePaths = $room->Images ?? [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('room-images', 'public');
                $imagePaths[] = $path;
            }
            $validated['Images'] = $imagePaths;
        }

        $room->update($validated);

        return redirect()->route('rooms.index')
            ->with('success', 'Room updated successfully!');
    }

    public function destroy($id)
    {
        $room = Room::findOrFail($id);
        $room->delete();

        return response()->json([
            'success' => true,
            'message' => 'Room moved to trash successfully!'
        ]);
    }

    public function destroyAll()
    {
        Room::query()->delete();

        return response()->json([
            'success' => true,
            'message' => 'All rooms moved to trash successfully!'
        ]);
    }

    // Trash methods
    public function trashIndex()
    {
        $trashedRooms = Room::onlyTrashed()->get();
        return view('rooms.trash', compact('trashedRooms'));
    }

    public function trashRestore($id)
    {
        $room = Room::onlyTrashed()->findOrFail($id);
        $room->restore();

        return response()->json([
            'success' => true,
            'message' => 'Room restored successfully!'
        ]);
    }

    public function trashDestroy($id)
    {
        $room = Room::onlyTrashed()->findOrFail($id);

        // Delete images from storage
        if ($room->Images) {
            foreach ($room->Images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $room->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Room permanently deleted!'
        ]);
    }

    public function trashEmpty()
    {
        $trashedRooms = Room::onlyTrashed()->get();

        foreach ($trashedRooms as $room) {
            // Delete images from storage
            if ($room->Images) {
                foreach ($room->Images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }
            $room->forceDelete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Trash emptied successfully!'
        ]);
    }
}
