@extends('layouts.app')
@section('content')
<div class="container-fluid py-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-defult">
                    <h2 class="card-title">
                        <i class="fa-solid fa-eye mr-2"></i>
                        Room Details - Room {{ $Room->RoomNo }}
                    </h2>
                    <div class="card-tools">
                        <a href="{{ route('room.edit', $Room->id) }}" class="btn bg-navy btn-sm">
                            <i class="fa-solid fa-edit mr-1"></i>Edit
                        </a>
                        <a href="{{ route('room.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fa-solid fa-arrow-left mr-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="border-bottom pb-2">Basic Information</h5>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="40%">Room Number:</th>
                                            <td><strong>Room {{ $Room->RoomNo }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Floor:</th>
                                            <td>{{ $Room->Floor }}</td>
                                        </tr>
                                        <tr>
                                            <th>Type:</th>
                                            <td>
                                                <span class="badge bg-{{ $Room->Type == 'Standard' ? 'secondary' : ($Room->Type == 'Deluxe' ? 'primary' : 'warning') }}">
                                                    {{ $Room->Type }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Price:</th>
                                            <td>${{ number_format($Room->Price, 2) }}/night</td>
                                        </tr>
                                        <tr>
                                            <th>Capacity:</th>
                                            <td>{{ $Room->Capacity }} Persons</td>
                                        </tr>
                                        <tr>
                                            <th>Status:</th>
                                            <td>
                                                <span class="badge bg-{{ $Room->Status == 'Available' ? 'success' : ($Room->Status == 'Occupied' ? 'danger' : ($Room->Status == 'Maintenance' ? 'warning' : 'info')) }}">
                                                    {{ $Room->Status }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="border-bottom pb-2">Amenities</h5>
                                    <div class="amenities-list">
                                        @foreach($Room->amenities as $amenity)
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fa-solid fa-check text-success mr-2"></i>
                                                <span>{{ $amenity }}</span>
                                            </div>
                                        @endforeach
                                        @if(count($Room->amenities) == 0)
                                            <p class="text-muted">No amenities added</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if($Room->Description)
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <h5 class="border-bottom pb-2">Description</h5>
                                    <p>{{ $Room->Description }}</p>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <h5 class="border-bottom pb-2">Room Images</h5>
                            @if($Room->Images && count($Room->Images) > 0)
                                <div class="row">
                                    @foreach($Room->Images as $image)
                                        <div class="col-md-6 mb-3">
                                            <img src="{{ asset('storage/' . $image) }}" class="img-thumbnail w-100" style="height: 150px; object-fit: cover;">
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fa-solid fa-image fa-3x mb-3"></i>
                                    <p>No images available</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fa-solid fa-calendar-alt mr-2"></i>
                                        Current Booking
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($Room->activeBooking)
                                        <div class="alert alert-warning">
                                            <strong>Currently Booked:</strong>
                                            {{ $Room->activeBooking->guest_name }}
                                            ({{ $Room->activeBooking->check_in->format('M d, Y') }} - {{ $Room->activeBooking->check_out->format('M d, Y') }})
                                        </div>
                                    @else
                                        <p class="text-muted mb-0">No active booking</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
