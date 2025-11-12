<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    // Display all bookings
    public function index()
    {
        $bookings = Booking::with('room')->latest()->get();
        return view('bookings.index', compact('bookings'));
    }

    // Show create form
    public function create()
    {
        $rooms = Room::where('status', 'Available')->get();
        return view('bookings.create', compact('rooms'));
    }

    // Store new booking
    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email',
            'guest_phone' => 'required|string|max:20',
            'guest_address' => 'nullable|string',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'special_requests' => 'nullable|string'
        ]);

        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $totalNights = $checkIn->diffInDays($checkOut);

        // Get room price
        $room = Room::findOrFail($request->room_id);
        $totalAmount = $room->Price * $totalNights;

        $booking = Booking::create([
            'room_id' => $request->room_id,
            'guest_name' => $request->guest_name,
            'guest_email' => $request->guest_email,
            'guest_phone' => $request->guest_phone,
            'guest_address' => $request->guest_address,
            'adults' => $request->adults,
            'children' => $request->children ?? 0,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'total_nights' => $totalNights,
            'total_amount' => $totalAmount,
            'paid_amount' => 0,
            'payment_status' => 'Pending',
            'status' => 'Confirmed',
            'special_requests' => $request->special_requests
        ]);

        // Update room status
        $room->update(['status' => 'Occupied']);

        return redirect()->route('bookings.index')
            ->with('success', 'Booking created successfully!');
    }

    // Show single booking (for AJAX)
    public function show($id)
    {
        $booking = Booking::with('room')->findOrFail($id);

        return response()->json([
            'success' => true,
            'booking' => $booking
        ]);
    }

    // Show edit form
    public function edit($id)
    {
        $booking = Booking::findOrFail($id);
        $rooms = Room::where('status', 'Available')->orWhere('id', $booking->room_id)->get();
        return view('bookings.edit', compact('booking', 'rooms'));
    }

    // Update booking
    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email',
            'guest_phone' => 'required|string|max:20',
            'guest_address' => 'nullable|string',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'special_requests' => 'nullable|string'
        ]);

        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $totalNights = $checkIn->diffInDays($checkOut);

        // Get room price
        $room = Room::findOrFail($request->room_id);
        $totalAmount = $room->Price * $totalNights;

        $booking->update([
            'room_id' => $request->room_id,
            'guest_name' => $request->guest_name,
            'guest_email' => $request->guest_email,
            'guest_phone' => $request->guest_phone,
            'guest_address' => $request->guest_address,
            'adults' => $request->adults,
            'children' => $request->children ?? 0,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'total_nights' => $totalNights,
            'total_amount' => $totalAmount,
            'special_requests' => $request->special_requests
        ]);

        return redirect()->route('bookings.index')
            ->with('success', 'Booking updated successfully!');
    }

    // Delete single booking (soft delete)
    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);

        // Free up the room
        $room = $booking->room;
        $room->update(['status' => 'Available']);

        $booking->delete();

        return response()->json([
            'success' => true,
            'message' => 'Booking moved to trash successfully!'
        ]);
    }

    // Delete all bookings (soft delete)
    public function destroyAll()
    {
        $bookings = Booking::all();

        foreach ($bookings as $booking) {
            // Free up the rooms
            $room = $booking->room;
            $room->update(['status' => 'Available']);
            $booking->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'All bookings moved to trash successfully!'
        ]);
    }

    // Check-in booking
    public function checkin($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'Checked In']);

        return response()->json([
            'success' => true,
            'message' => 'Guest checked in successfully!'
        ]);
    }

    // Check-out booking
    public function checkout($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'Checked Out']);

        // Free up the room
        $room = $booking->room;
        $room->update(['status' => 'Available']);

        return response()->json([
            'success' => true,
            'message' => 'Guest checked out successfully!'
        ]);
    }

    // Cancel booking
    public function cancel(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $booking->update([
            'status' => 'Cancelled',
            'cancellation_reason' => $request->reason
        ]);

        // Free up the room
        $room = $booking->room;
        $room->update(['status' => 'Available']);

        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled successfully!'
        ]);
    }

    // Show trash
    public function trashIndex()
    {
        $trashedBookings = Booking::onlyTrashed()->with('room')->latest()->get();
        return view('bookings.trash', compact('trashedBookings'));
    }

    // Restore from trash
    public function trashRestore($id)
    {
        $booking = Booking::onlyTrashed()->findOrFail($id);
        $booking->restore();

        // Mark room as occupied again
        $room = $booking->room;
        $room->update(['status' => 'Occupied']);

        return response()->json([
            'success' => true,
            'message' => 'Booking restored successfully!'
        ]);
    }

    // Permanently delete from trash
    public function trashDestroy($id)
    {
        $booking = Booking::onlyTrashed()->findOrFail($id);
        $booking->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Booking permanently deleted!'
        ]);
    }

    // Empty trash
    public function trashEmpty()
    {
        $trashedBookings = Booking::onlyTrashed()->get();

        foreach ($trashedBookings as $booking) {
            $booking->forceDelete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Trash emptied successfully!'
        ]);
    }
}
