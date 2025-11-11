@extends('layouts.app')
@section('content')
<div class="container-fluid py-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-defult">
                    <h2 class="card-title">
                        <i class="fa-solid fa-edit mr-2"></i>
                        Edit Transaction - {{ $Transaction->reference_number }}
                    </h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('transactions.update', $Transaction->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="border-bottom pb-2">Transaction Details</h5>

                                <div class="form-group">
                                    <label>Transaction Type *</label>
                                    <select name="type" class="form-control" id="type" required>
                                        <option value="">Select Type</option>
                                        <option value="Income" {{ $Transaction->type == 'Income' ? 'selected' : '' }}>Income</option>
                                        <option value="Expense" {{ $Transaction->type == 'Expense' ? 'selected' : '' }}>Expense</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Category *</label>
                                    <select name="category" class="form-control" id="category" required>
                                        <option value="">Select Category</option>
                                        <!-- Categories will be populated by JavaScript -->
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Description *</label>
                                    <textarea name="description" class="form-control" rows="3" required placeholder="Enter transaction description">{{ $Transaction->description }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label>Amount ($) *</label>
                                    <input type="number" name="amount" class="form-control" value="{{ $Transaction->amount }}" step="0.01" min="0" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5 class="border-bottom pb-2">Additional Information</h5>

                                <div class="form-group">
                                    <label>Transaction Date *</label>
                                    <input type="date" name="transaction_date" class="form-control" value="{{ $Transaction->transaction_date->format('Y-m-d') }}" required>
                                </div>

                                <div class="form-group">
                                    <label>Payment Method *</label>
                                    <select name="payment_method" class="form-control" required>
                                        <option value="Cash" {{ $Transaction->payment_method == 'Cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="Card" {{ $Transaction->payment_method == 'Card' ? 'selected' : '' }}>Card</option>
                                        <option value="Bank Transfer" {{ $Transaction->payment_method == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        <option value="Digital Wallet" {{ $Transaction->payment_method == 'Digital Wallet' ? 'selected' : '' }}>Digital Wallet</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Related Booking (Optional)</label>
                                    <select name="booking_id" class="form-control">
                                        <option value="">Select Booking</option>
                                        @foreach($bookings as $booking)
                                            <option value="{{ $booking->id }}" {{ $Transaction->booking_id == $booking->id ? 'selected' : '' }}>
                                                {{ $booking->reference_number }} - {{ $booking->guest_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Notes</label>
                                    <textarea name="notes" class="form-control" rows="3" placeholder="Additional notes (optional)">{{ $Transaction->notes }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn bg-navy">
                                <i class="fa-solid fa-save mr-2"></i>Update Transaction
                            </button>
                            <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                                <i class="fa-solid fa-times mr-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const categorySelect = document.getElementById('category');

    const categories = @json($categories);
    const currentCategory = @json($Transaction->category);

    function updateCategories() {
        const selectedType = typeSelect.value;
        categorySelect.innerHTML = '<option value="">Select Category</option>';

        if (selectedType && categories[selectedType]) {
            categories[selectedType].forEach(category => {
                const option = document.createElement('option');
                option.value = category;
                option.textContent = category;
                if (category === currentCategory) {
                    option.selected = true;
                }
                categorySelect.appendChild(option);
            });
        }
    }

    typeSelect.addEventListener('change', updateCategories);

    // Initialize categories
    updateCategories();
});
</script>
@endsection
