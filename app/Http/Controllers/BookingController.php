<?php
// app/Http/Controllers/BookingController.php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BookingController extends Controller
{
    // Index - Display booking management page
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getBookings($request);
        }

        $bookings = Booking::with('room')->latest()->get();
        return view('bookings.index', compact('bookings'));
    }

    // Create - Show booking creation form
    public function create()
    {
        $rooms = Room::where('Status', 'Available')->get();
        return view('bookings.create', compact('rooms'));
    }

    // Get bookings for DataTables
    public function getBookings(Request $request)
    {
        try {
            $data = Booking::with('room')->latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '
                    <div class="btn-group">
                        <button class="btn btn-sm btn-info view-btn" data-id="' . $row->id . '" title="View">
                            <i class="fa fa-eye"></i>
                        </button>
                        <a href="' . route('bookings.edit', $row->id) . '" class="btn btn-sm btn-warning edit-btn" title="Edit">
                            <i class="fa fa-edit"></i>
                        </a>';

                    // Additional actions based on status
                    if ($row->status == 'Confirmed') {
                        $actionBtn .= '
                        <button class="btn btn-sm btn-success checkin-btn" data-id="' . $row->id . '" title="Check In">
                            <i class="fa fa-sign-in"></i>
                        </button>
                        <button class="btn btn-sm btn-danger cancel-btn" data-id="' . $row->id . '" title="Cancel">
                            <i class="fa fa-times"></i>
                        </button>';
                    } elseif ($row->status == 'Checked In') {
                        $actionBtn .= '
                        <button class="btn btn-sm btn-primary checkout-btn" data-id="' . $row->id . '" title="Check Out">
                            <i class="fa fa-sign-out"></i>
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
                        case 'Confirmed':
                            $badgeClass .= 'bg-success';
                            break;
                        case 'Checked In':
                            $badgeClass .= 'bg-primary';
                            break;
                        case 'Checked Out':
                            $badgeClass .= 'bg-info';
                            break;
                        case 'Cancelled':
                            $badgeClass .= 'bg-danger';
                            break;
                        default:
                            $badgeClass .= 'bg-secondary';
                    }
                    return '<span class="' . $badgeClass . '">' . $row->status . '</span>';
                })
                ->editColumn('payment_status', function ($row) {
                    $badgeClass = 'badge ';
                    switch ($row->payment_status) {
                        case 'Paid':
                            $badgeClass .= 'bg-success';
                            break;
                        case 'Pending':
                            $badgeClass .= 'bg-warning';
                            break;
                        case 'Partial':
                            $badgeClass .= 'bg-info';
                            break;
                        case 'Refunded':
                            $badgeClass .= 'bg-secondary';
                            break;
                        default:
                            $badgeClass .= 'bg-secondary';
                    }
                    return '<span class="' . $badgeClass . '">' . $row->payment_status . '</span>';
                })
                ->editColumn('total_amount', function ($row) {
                    return '$' . number_format($row->total_amount, 2);
                })
                ->editColumn('paid_amount', function ($row) {
                    return '$' . number_format($row->paid_amount, 2);
                })
                ->editColumn('room.RoomNo', function ($row) {
                    return $row->room->RoomNo . ' (' . $row->room->Type . ')';
                })
                ->editColumn('check_in', function ($row) {
                    return Carbon::parse($row->check_in)->format('M d, Y');
                })
                ->editColumn('check_out', function ($row) {
                    return Carbon::parse($row->check_out)->format('M d, Y');
                })
                ->rawColumns(['action', 'status', 'payment_status'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error in getBookings:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch bookings'], 500);
        }
    }

    // Show - Display single booking
    public function show($id)
    {
        try {
            $booking = Booking::with('room')->findOrFail($id);
            return response()->json([
                'success' => true,
                'booking' => $booking
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }
    }

    // Edit - Get booking data for editing
    public function edit($id)
    {
        try {
            $booking = Booking::with('room')->findOrFail($id);
            $rooms = Room::where('Status', 'Available')
                ->orWhere('id', $booking->room_id)
                ->get();
            return view('bookings.edit', compact('booking', 'rooms'));
        } catch (\Exception $e) {
            return redirect()->route('bookings.index')
                ->with('error', 'Booking not found');
        }
    }

    // Store - Create new booking
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'required|string|max:20',
            'guest_address' => 'nullable|string|max:500',
            'adults' => 'required|integer|min:1|max:10',
            'children' => 'nullable|integer|min:0|max:10',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'special_requests' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Check room availability
            if (!Booking::isRoomAvailable($request->room_id, $request->check_in, $request->check_out)) {
                return redirect()->back()
                    ->with('error', 'Selected room is not available for the chosen dates.')
                    ->withInput();
            }

            $bookingData = $request->only([
                'room_id',
                'guest_name',
                'guest_email',
                'guest_phone',
                'guest_address',
                'adults',
                'children',
                'check_in',
                'check_out',
                'special_requests'
            ]);

            // Calculate total nights and amount
            $room = Room::find($request->room_id);
            $checkIn = Carbon::parse($request->check_in);
            $checkOut = Carbon::parse($request->check_out);
            $totalNights = $checkIn->diffInDays($checkOut);
            $totalAmount = $room->Price * $totalNights;

            $bookingData['total_nights'] = $totalNights;
            $bookingData['total_amount'] = $totalAmount;

            // Create booking
            $booking = Booking::create($bookingData);

            // Update room status if needed
            $room->update(['Status' => 'Occupied']);

            return redirect()->route('bookings.index')
                ->with('success', 'Booking created successfully!');
        } catch (\Exception $e) {
            Log::error('Booking creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create booking: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Update - Edit booking
    public function update(Request $request, $id)
    {
        try {
            $booking = Booking::with('room')->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'room_id' => 'required|exists:rooms,id',
                'guest_name' => 'required|string|max:255',
                'guest_email' => 'required|email|max:255',
                'guest_phone' => 'required|string|max:20',
                'guest_address' => 'nullable|string|max:500',
                'adults' => 'required|integer|min:1|max:10',
                'children' => 'nullable|integer|min:0|max:10',
                'check_in' => 'required|date',
                'check_out' => 'required|date|after:check_in',
                'special_requests' => 'nullable|string|max:1000'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Check room availability (excluding current booking)
            if (!Booking::isRoomAvailable($request->room_id, $request->check_in, $request->check_out, $booking->id)) {
                return redirect()->back()
                    ->with('error', 'Selected room is not available for the chosen dates.')
                    ->withInput();
            }

            $bookingData = $request->only([
                'room_id',
                'guest_name',
                'guest_email',
                'guest_phone',
                'guest_address',
                'adults',
                'children',
                'check_in',
                'check_out',
                'special_requests'
            ]);

            // Calculate total nights and amount if dates or room changed
            if (
                $booking->room_id != $request->room_id ||
                $booking->check_in != $request->check_in ||
                $booking->check_out != $request->check_out
            ) {

                $room = Room::find($request->room_id);
                $checkIn = Carbon::parse($request->check_in);
                $checkOut = Carbon::parse($request->check_out);
                $totalNights = $checkIn->diffInDays($checkOut);
                $totalAmount = $room->Price * $totalNights;

                $bookingData['total_nights'] = $totalNights;
                $bookingData['total_amount'] = $totalAmount;
            }

            $booking->update($bookingData);

            return redirect()->route('bookings.index')
                ->with('success', 'Booking updated successfully!');
        } catch (\Exception $e) {
            Log::error('Booking update failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update booking: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Check In
    public function checkIn($id)
    {
        try {
            $booking = Booking::findOrFail($id);

            if ($booking->status != 'Confirmed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only confirmed bookings can be checked in.'
                ]);
            }

            $booking->update(['status' => 'Checked In']);

            return response()->json([
                'success' => true,
                'message' => 'Guest checked in successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check in: ' . $e->getMessage()
            ], 500);
        }
    }

    // Check Out
    public function checkOut($id)
    {
        try {
            $booking = Booking::with('room')->findOrFail($id);

            if ($booking->status != 'Checked In') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only checked-in bookings can be checked out.'
                ]);
            }

            $booking->update(['status' => 'Checked Out']);

            // Update room status back to available
            $booking->room->update(['Status' => 'Available']);

            return response()->json([
                'success' => true,
                'message' => 'Guest checked out successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check out: ' . $e->getMessage()
            ], 500);
        }
    }

    // Cancel Booking
    public function cancel(Request $request, $id)
    {
        try {
            $booking = Booking::with('room')->findOrFail($id);

            if (!in_array($booking->status, ['Confirmed', 'Checked In'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only confirmed or checked-in bookings can be cancelled.'
                ]);
            }

            $booking->update([
                'status' => 'Cancelled',
                'cancellation_reason' => $request->reason
            ]);

            // Update room status back to available
            $booking->room->update(['Status' => 'Available']);

            return response()->json([
                'success' => true,
                'message' => 'Booking cancelled successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel booking: ' . $e->getMessage()
            ], 500);
        }
    }

    // Add Payment
    public function addPayment(Request $request, $id)
    {
        try {
            $booking = Booking::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:0',
                'payment_type' => 'required|in:cash,card,transfer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed!',
                    'errors' => $validator->errors()
                ], 422);
            }

            $newPaidAmount = $booking->paid_amount + $request->amount;

            // Update payment status
            $paymentStatus = 'Partial';
            if ($newPaidAmount >= $booking->total_amount) {
                $paymentStatus = 'Paid';
            } elseif ($newPaidAmount == 0) {
                $paymentStatus = 'Pending';
            }

            $booking->update([
                'paid_amount' => $newPaidAmount,
                'payment_status' => $paymentStatus
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment added successfully!',
                'booking' => $booking
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add payment: ' . $e->getMessage()
            ], 500);
        }
    }

    // Check Room Availability
    public function checkAvailability(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'room_id' => 'required|exists:rooms,id',
                'check_in' => 'required|date',
                'check_out' => 'required|date|after:check_in',
                'booking_id' => 'nullable|exists:bookings,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed!'
                ], 422);
            }

            $isAvailable = Booking::isRoomAvailable(
                $request->room_id,
                $request->check_in,
                $request->check_out,
                $request->booking_id
            );

            if ($isAvailable) {
                $room = Room::find($request->room_id);
                $nights = Carbon::parse($request->check_in)->diffInDays(Carbon::parse($request->check_out));
                $totalAmount = $room->Price * $nights;

                return response()->json([
                    'success' => true,
                    'available' => true,
                    'total_nights' => $nights,
                    'total_amount' => $totalAmount,
                    'room_price' => $room->Price
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'available' => false,
                    'message' => 'Room is not available for the selected dates.'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking availability: ' . $e->getMessage()
            ], 500);
        }
    }

    // Destroy - Soft delete booking
    public function destroy($id)
    {
        try {
            $booking = Booking::findOrFail($id);
            $booking->delete();

            return response()->json([
                'success' => true,
                'message' => 'Booking moved to trash successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Booking deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete booking: ' . $e->getMessage()
            ], 500);
        }
    }

    // Delete All - Soft delete all bookings
    public function deleteAll()
    {
        try {
            Booking::query()->delete();

            return response()->json([
                'success' => true,
                'message' => 'All bookings moved to trash successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Delete all bookings failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete all bookings: ' . $e->getMessage()
            ], 500);
        }
    }

    // Trash - View trashed bookings
    public function trash()
    {
        $trashedBookings = Booking::onlyTrashed()->with('room')->get();
        return view('bookings.trash', compact('trashedBookings'));
    }

    // Restore - Restore from trash
    public function restore($id)
    {
        try {
            $booking = Booking::onlyTrashed()->findOrFail($id);
            $booking->restore();

            return response()->json([
                'success' => true,
                'message' => 'Booking restored successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore booking: ' . $e->getMessage()
            ], 500);
        }
    }

    // Force Delete - Permanently delete
    public function forceDelete($id)
    {
        try {
            $booking = Booking::onlyTrashed()->findOrFail($id);
            $booking->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Booking permanently deleted!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete booking: ' . $e->getMessage()
            ], 500);
        }
    }
}
