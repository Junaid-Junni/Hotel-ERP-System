@extends('layouts.app')

@section('content')
<h2>Edit Roles for {{ $user->name }}</h2>
<form method="POST" action="{{ route('admin.roles.update', $user->id) }}">
    @csrf
    @foreach($roles as $role)
        <label>
            <input type="checkbox" name="roles[]" value="{{ $role->name }}" 
                {{ $user->hasRole($role->name) ? 'checked' : '' }}>
            {{ $role->name }}
        </label><br>
    @endforeach
    <button type="submit">Update Roles</button>
</form>
@endsection
