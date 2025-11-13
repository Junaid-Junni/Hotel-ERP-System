<!-- Home -->
<li class="nav-item">
    <a href="{{ route('home') }}" class="nav-link {{ Request::is('home') ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Home</p>
    </a>
</li>

{{-- <--Rooms--> --}}
<li class="nav-item">
    <a href="{{ route('rooms.index') }}" class="nav-link {{ Request::is('rooms*') ? 'active' : '' }}">
        <span class="material-symbols-outlined nav-icon">
            meeting_room
        </span>
        <p>Rooms</p>
    </a>
</li>


{{-- <--Bookings-->> --}}
<li class="nav-item">
    <a href="{{ route('bookings.index') }}" class="nav-link {{ Request::is('bookings*') ? 'active' : '' }}">
        <span class="material-symbols-outlined nav-icon">
            book_online
        </span>
        <p>Bookings</p>
    </a>
</li>

{{-- <--User--> --}}
<li class="nav-item">
    <a href="{{ route('user.index') }}" class="nav-link {{ Request::is('user') ? 'active' : '' }}">
        <i class="fa-solid fa-user nav-icon fas"></i>
        <p>User</p>
    </a>
</li>

@role('admin')
{{-- <--Admin Panel--> --}}
{{-- roles  --}}
<li class="nav-item">
    <a href="{{ route('admin.roles.roles') }}" class="nav-link {{ Request::is('admin') ? 'active' : '' }}">
        <i class="fa-solid fa-user nav-icon fas"></i>
        <p>Roles</p>
    </a>
</li>
@endrole

@role('staff')
{{-- <--Admin Panel--> --}}
{{-- roles  --}}
<li class="nav-item">
    <a href="{{ route('admin.roles.roles') }}" class="nav-link {{ Request::is('admin') ? 'active' : '' }}">
        <i class="fa-solid fa-user nav-icon fas"></i>
        <p>Test Staff Roll</p>
    </a>
</li>
@endrole

@role('user')
{{-- <--Admin Panel--> --}}
{{-- roles  --}}
<li class="nav-item">
    <a href="{{ route('admin.roles.roles') }}" class="nav-link {{ Request::is('admin') ? 'active' : '' }}">
        <i class="fa-solid fa-user nav-icon fas"></i>
        <p>Test User Roll</p>
    </a>
</li>
@endrole



{{-- <--Employees--> --}}
<li class="nav-item">
    <a href="{{ route('employees.index') }}" class="nav-link {{ Request::is('employees*') ? 'active' : '' }}">
        <span class="material-symbols-outlined nav-icon">
            groups
        </span>
        <p>Employees</p>
    </a>
</li>

{{-- <--Invoices--> --}}
{{-- <li class="nav-item">
    <a href="#" class="nav-link {{ Request::is('invoice') ? 'active' : '' }}">
        <i class="fa-solid fa-file-invoice-dollar nav-icon fas"></i>
        <p>Invoice</p>
    </a>
</li> --}}
<!-- Transactions Menu -->
<li class="nav-item">
    <a href="{{ route('transactions.index') }}" class="nav-link {{ Request::is('transactions*') ? 'active' : '' }}">
        <i class="fa-solid fa-money-bill-transfer nav-icon fas"></i>
        <p>Transactions</p>
    </a>
</li>

{{-- <--Room Transfer--> --}}
{{-- <li class="nav-item">
    <a href="#" class="nav-link {{ Request::is('roomTransfer') ? 'active' : '' }}">
        <span class="material-symbols-outlined nav-icon fes">
            transfer_within_a_station
        </span>
        <p class="p-0 m-0">Room Transfer</p>
    </a>
</li> --}}
{{-- <--Inventory--> --}}
<li class="nav-item">
    <a href="{{ route('inventory.index') }}" class="nav-link {{ Request::is('inventory*') ? 'active' : '' }}">
        <i class="fa-solid fa-boxes-stacked nav-icon fas"></i>
        <p>Inventory</p>
    </a>
</li>
{{-- <--Housekeeping--> --}}
<li class="nav-item">
    <a href="{{ route('housekeeping.index') }}" class="nav-link {{ Request::is('housekeeping*') ? 'active' : '' }}">
        <span class="material-symbols-outlined nav-icon">
            cleaning_services
        </span>
        <p>Housekeeping</p>
    </a>
</li>
