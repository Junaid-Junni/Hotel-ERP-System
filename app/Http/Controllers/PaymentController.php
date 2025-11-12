<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    // Show payment form
    public function create($bookingId)
    {
        $booking = Booking::with('room', 'payments')->findOrFail($bookingId);
        return view('payments.create', compact('booking'));
    }

    // Store payment
    public function store(Request $request, $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . ($booking->total_amount - $booking->total_paid),
            'payment_method' => 'required|in:cash,digital',
            'digital_type' => 'required_if:payment_method,digital',
            'transaction_id' => 'nullable|string|max:255',
            'screenshot' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'notes' => 'nullable|string|max:500',
            'payment_date' => 'required|date'
        ]);

        // Generate unique payment number
        $paymentNumber = 'PAY-' . strtoupper(Str::random(8));

        $paymentData = [
            'booking_id' => $booking->id,
            'payment_number' => $paymentNumber,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'payment_date' => $request->payment_date,
            'notes' => $request->notes,
            'status' => 'completed'
        ];

        // Handle digital payment details
        if ($request->payment_method === 'digital') {
            $paymentData['digital_type'] = $request->digital_type;
            $paymentData['transaction_id'] = $request->transaction_id;

            // Handle screenshot upload
            if ($request->hasFile('screenshot')) {
                $screenshotPath = $request->file('screenshot')->store('payment-proofs', 'public');
                $paymentData['screenshot_path'] = $screenshotPath;
            }
        }

        // Create payment
        Payment::create($paymentData);

        // Update booking paid amount
        $booking->update([
            'paid_amount' => $booking->payments()->completed()->sum('amount')
        ]);

        // Update payment status if fully paid
        if ($booking->is_fully_paid) {
            $booking->update(['payment_status' => 'Paid']);
        } else {
            $booking->update(['payment_status' => 'Partial']);
        }

        return redirect()->route('bookings.show', $booking->id)
            ->with('success', 'Payment added successfully!');
    }

    // Show payment details
    public function show($id)
    {
        $payment = Payment::with('booking.room')->findOrFail($id);
        return view('payments.show', compact('payment'));
    }

    // Delete payment
    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $booking = $payment->booking;

        // Delete screenshot if exists
        if ($payment->screenshot_path) {
            Storage::disk('public')->delete($payment->screenshot_path);
        }

        $payment->delete();

        // Update booking payment status
        $booking->update([
            'paid_amount' => $booking->payments()->completed()->sum('amount')
        ]);

        if ($booking->paid_amount == 0) {
            $booking->update(['payment_status' => 'Pending']);
        } elseif ($booking->is_fully_paid) {
            $booking->update(['payment_status' => 'Paid']);
        } else {
            $booking->update(['payment_status' => 'Partial']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment deleted successfully!'
        ]);
    }

    // Download payment proof
    public function downloadProof($id)
    {
        $payment = Payment::findOrFail($id);

        if (!$payment->screenshot_path) {
            return redirect()->back()->with('error', 'No proof available for this payment.');
        }

        return Storage::disk('public')->download($payment->screenshot_path);
    }
}
