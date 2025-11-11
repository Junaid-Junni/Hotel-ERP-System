<div class="btn-group">
    <button type="button" class="btn btn-info btn-sm ViewBtn" data-id="{{ $id }}" title="View">
        <i class="fa-solid fa-eye"></i>
    </button>
    <a href="/room/{{ $id }}/edit" class="btn bg-navy btn-sm" title="Edit">
        <i class="fa-solid fa-edit"></i>
    </a>
    <button type="button" class="btn btn-danger btn-sm DeleteBtn" data-id="{{ $id }}" title="Delete">
        <i class="fa-solid fa-trash"></i>
    </button>
</div>
{{-- <-- booking btn --> --}}
<div class="btn-group">
    <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-sm btn-info" title="View">
        <i class="fa fa-eye"></i>
    </a>
    <a href="{{ route('bookings.edit', $booking->id) }}" class="btn btn-sm btn-warning" title="Edit">
        <i class="fa fa-edit"></i>
    </a>

    @if($booking->booking_status == 'Confirmed')
        <form action="{{ route('bookings.checkin', $booking->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-success" title="Check In">
                <i class="fa fa-sign-in-alt"></i>
            </button>
        </form>
    @endif

    @if($booking->booking_status == 'Checked In')
        <form action="{{ route('bookings.checkout', $booking->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-primary" title="Check Out">
                <i class="fa fa-sign-out-alt"></i>
            </button>
        </form>
    @endif

    @if(in_array($booking->booking_status, ['Confirmed', 'Checked In']))
        <form action="{{ route('bookings.cancel', $booking->id) }}" method="POST" class="d-inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-sm btn-danger" title="Cancel"
                    onclick="return confirm('Are you sure you want to cancel this booking?')">
                <i class="fa fa-times"></i>
            </button>
        </form>
    @endif

    <form action="{{ route('bookings.destroy', $booking->id) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger" title="Delete"
                onclick="return confirm('Are you sure you want to move this booking to trash?')">
            <i class="fa fa-trash"></i>
        </button>
    </form>
</div>
{{-- <--Transaction Buttons--> --}}
<div class="btn-group">
    <button type="button" class="btn btn-info btn-sm ViewBtn" data-id="{{ $id }}" title="View">
        <i class="fa-solid fa-eye"></i>
    </button>

    @php
        $currentRoute = request()->segment(1); // Gets the first URL segment
    @endphp

    @if($currentRoute == 'room')
        <a href="{{ route('room.edit', $id) }}" class="btn bg-navy btn-sm" title="Edit">
            <i class="fa-solid fa-edit"></i>
        </a>
        <button type="button" class="btn btn-danger btn-sm DeleteBtn" data-id="{{ $id }}" title="Delete">
            <i class="fa-solid fa-trash"></i>
        </button>
    @elseif($currentRoute == 'booking')
        <a href="{{ route('booking.edit', $id) }}" class="btn bg-navy btn-sm" title="Edit">
            <i class="fa-solid fa-edit"></i>
        </a>
        <button type="button" class="btn btn-danger btn-sm DeleteBtn" data-id="{{ $id }}" title="Delete">
            <i class="fa-solid fa-trash"></i>
        </button>
    @elseif($currentRoute == 'transaction')
        <a href="{{ route('transaction.edit', $id) }}" class="btn bg-navy btn-sm" title="Edit">
            <i class="fa-solid fa-edit"></i>
        </a>
        <button type="button" class="btn btn-danger btn-sm DeleteBtn" data-id="{{ $id }}" title="Delete">
            <i class="fa-solid fa-trash"></i>
        </button>
    @endif
</div>

{{-- <--Inventory--> --}}
<div class="btn-group">
    <a href="{{ route('inventory.show', $row->id) }}" class="btn btn-sm btn-info" title="View">
        <i class="fa fa-eye"></i>
    </a>
    <a href="{{ route('inventory.edit', $row->id) }}" class="btn btn-sm btn-warning" title="Edit">
        <i class="fa fa-edit"></i>
    </a>
    <button class="btn btn-sm btn-primary StockBtn"
            data-item-id="{{ $row->id }}"
            data-item-name="{{ $row->name }}"
            data-current-stock="{{ $row->quantity }}"
            title="Adjust Stock">
        <i class="fa fa-warehouse"></i>
    </button>
    <form action="{{ route('inventory.destroy', $row->id) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger" title="Delete"
                onclick="return confirm('Are you sure you want to move this item to trash?')">
            <i class="fa fa-trash"></i>
        </button>
    </form>
</div>
