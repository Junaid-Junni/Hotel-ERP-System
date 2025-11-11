<?php
// app/Http/Controllers/GuestController.php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;

class GuestController extends Controller
{
    public function index()
    {
        return view('guests.index');
    }

    public function create()
    {
        return view('guests.create');
    }

    public function getGuests(Request $request)
    {
        if ($request->ajax()) {
            $data = Guest::latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '
                    <div class="btn-group">
                        <button class="btn btn-sm btn-info ViewBtn" data-id="' . $row->id . '" title="View">
                            <i class="fa fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-warning EditBtn" data-id="' . $row->id . '" title="Edit">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger DeleteBtn" data-id="' . $row->id . '" title="Delete">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>';
                    return $actionBtn;
                })
                ->addColumn('full_name', function ($row) {
                    return $row->first_name . ' ' . $row->last_name;
                })
                ->editColumn('date_of_birth', function ($row) {
                    return $row->date_of_birth ? $row->date_of_birth->format('M d, Y') : 'N/A';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return response()->json(['error' => 'Not an AJAX request'], 400);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:guests,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:50',
            'country' => 'required|string|max:50',
            'id_type' => 'required|string|in:Passport,Driving License,National ID,Other',
            'id_number' => 'required|string|max:50',
            'date_of_birth' => 'nullable|date',
            'nationality' => 'required|string|max:50',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $guest = Guest::create($request->all());

            return redirect()->route('guest.index')
                ->with('success', 'Guest created successfully!');
        } catch (\Exception $e) {
            Log::error('Guest creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create guest: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        try {
            $guest = Guest::findOrFail($id);
            return response()->json($guest);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Guest not found'], 404);
        }
    }

    public function edit($id)
    {
        try {
            $guest = Guest::findOrFail($id);
            return view('guests.edit', compact('guest'));
        } catch (\Exception $e) {
            return redirect()->route('guest.index')
                ->with('error', 'Guest not found');
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:guests,email,' . $id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:50',
            'country' => 'required|string|max:50',
            'id_type' => 'required|string|in:Passport,Driving License,National ID,Other',
            'id_number' => 'required|string|max:50',
            'date_of_birth' => 'nullable|date',
            'nationality' => 'required|string|max:50',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $guest = Guest::findOrFail($id);
            $guest->update($request->all());

            return redirect()->route('guest.index')
                ->with('success', 'Guest updated successfully!');
        } catch (\Exception $e) {
            Log::error('Guest update failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update guest: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $guest = Guest::findOrFail($id);
            $guest->delete();

            return response()->json([
                'success' => true,
                'message' => 'Guest moved to trash successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Guest deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete guest: ' . $e->getMessage()
            ], 500);
        }
    }
}
