@extends('layouts.app')

@section('content')
<h2>Roles & Permissions</h2>
@if(session('success'))
    <div>{{ session('success') }}</div>
@endif

@foreach($roles as $role)
    <h3>{{ $role->name }}</h3>
    <form method="POST" action="{{ route('admin.roles.permissions.update', $role->id) }}">
        @csrf
        @foreach($permissions as $permission)
            <label>
                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                    {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                {{ $permission->name }}
            </label><br>
        @endforeach
        <button type="submit">Update Permissions</button>
    </form>
    <hr>
@endforeach
@endsection
