@extends('layouts.app')

@section('content')
<h2>Users & Roles</h2>
@if(session('success'))
    <div>{{ session('success') }}</div>
@endif
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Roles</th>
        <th>Action</th>
    </tr>
    @foreach($users as $user)
    <tr>
        <td>{{ $user->name }}</td>
        <td>{{ $user->email }}</td>
        <td>{{ implode(', ', $user->roles->pluck('name')->toArray()) }}</td>
        <td><a href="{{ route('admin.roles.edit', $user->id) }}">Edit Roles</a></td>
    </tr>
    @endforeach
</table>
@endsection
