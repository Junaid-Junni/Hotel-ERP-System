{{-- resources/views/rooms/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Room')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Edit Room: {{ $room->RoomNo }}</h3>
                        <a href="{{ route('rooms.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back to Rooms
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('rooms.update', $room->id) }}" method="POST" enctype="multipart/form-data" id="editForm">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="RoomNo">Room Number *</label>
                                    <select class="form-control @error('RoomNo') is-invalid @enderror" id="RoomNo" name="RoomNo" required>
                                        <option value="">Select Room Number</option>
                                        @for($i = 1; $i <= 29; $i++)
                                            <option value="{{ $i }}" {{ old('RoomNo', $room->RoomNo) == $i ? 'selected' : '' }}>Room {{ $i }}</option>
                                        @endfor
                                    </select>
                                    @error('RoomNo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="Floor">Floor *</label>
                                    <select class="form-control @error('Floor') is-invalid @enderror" id="Floor" name="Floor" required>
                                        <option value="">Select Floor</option>
                                        <option value="1st Floor" {{ old('Floor', $room->Floor) == '1st Floor' ? 'selected' : '' }}>1st Floor</option>
                                        <option value="2nd Floor" {{ old('Floor', $room->Floor) == '2nd Floor' ? 'selected' : '' }}>2nd Floor</option>
                                        <option value="3rd Floor" {{ old('Floor', $room->Floor) == '3rd Floor' ? 'selected' : '' }}>3rd Floor</option>
                                        <option value="4th Floor" {{ old('Floor', $room->Floor) == '4th Floor' ? 'selected' : '' }}>4th Floor</option>
                                    </select>
                                    @error('Floor')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="Type">Room Type *</label>
                                    <select class="form-control @error('Type') is-invalid @enderror" id="Type" name="Type" required>
                                        <option value="">Select Type</option>
                                        <option value="Standard" {{ old('Type', $room->Type) == 'Standard' ? 'selected' : '' }}>Standard</option>
                                        <option value="Deluxe" {{ old('Type', $room->Type) == 'Deluxe' ? 'selected' : '' }}>Deluxe</option>
                                        <option value="Suite" {{ old('Type', $room->Type) == 'Suite' ? 'selected' : '' }}>Suite</option>
                                    </select>
                                    @error('Type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="Price">Price ($) *</label>
                                    <input type="number" step="0.01" class="form-control @error('Price') is-invalid @enderror"
                                           id="Price" name="Price" value="{{ old('Price', $room->Price) }}" required>
                                    @error('Price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Capacity">Capacity *</label>
                                    <select class="form-control @error('Capacity') is-invalid @enderror" id="Capacity" name="Capacity" required>
                                        <option value="">Select Capacity</option>
                                        <option value="1" {{ old('Capacity', $room->Capacity) == '1' ? 'selected' : '' }}>1 Person</option>
                                        <option value="2" {{ old('Capacity', $room->Capacity) == '2' ? 'selected' : '' }}>2 Persons</option>
                                        <option value="3" {{ old('Capacity', $room->Capacity) == '3' ? 'selected' : '' }}>3 Persons</option>
                                        <option value="4" {{ old('Capacity', $room->Capacity) == '4' ? 'selected' : '' }}>4 Persons</option>
                                    </select>
                                    @error('Capacity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="Status">Status *</label>
                                    <select class="form-control @error('Status') is-invalid @enderror" id="Status" name="Status" required>
                                        <option value="Available" {{ old('Status', $room->Status) == 'Available' ? 'selected' : '' }}>Available</option>
                                        <option value="Occupied" {{ old('Status', $room->Status) == 'Occupied' ? 'selected' : '' }}>Occupied</option>
                                        <option value="Maintenance" {{ old('Status', $room->Status) == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        <option value="Cleaning" {{ old('Status', $room->Status) == 'Cleaning' ? 'selected' : '' }}>Cleaning</option>
                                    </select>
                                    @error('Status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="Description">Description</label>
                                    <textarea class="form-control @error('Description') is-invalid @enderror"
                                              id="Description" name="Description" rows="3">{{ old('Description', $room->Description) }}</textarea>
                                    @error('Description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="Images">Room Images</label>
                                    <input type="file" class="form-control-file @error('Images.*') is-invalid @enderror"
                                           id="Images" name="Images[]" multiple accept="image/*">
                                    @error('Images.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Multiple images allowed. Max size: 2MB per image.</small>

                                    @if($room->Images)
                                        <div class="mt-2">
                                            <label>Current Images:</label>
                                            <div class="row">
                                                @foreach(json_decode($room->Images) as $image)
                                                    <div class="col-md-3 mb-2">
                                                        <img src="{{ Storage::url($image) }}" class="img-thumbnail" style="height: 100px; object-fit: cover;">
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Amenities</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" id="AC" name="AC" value="1" {{ old('AC', $room->AC) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="AC">Air Conditioning</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" id="TV" name="TV" value="1" {{ old('TV', $room->TV) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="TV">TV</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" id="WiFi" name="WiFi" value="1" {{ old('WiFi', $room->WiFi) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="WiFi">WiFi</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" id="Geyser" name="Geyser" value="1" {{ old('Geyser', $room->Geyser) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="Geyser">Geyser</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" id="Balcony" name="Balcony" value="1" {{ old('Balcony', $room->Balcony) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="Balcony">Balcony</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" id="Intercom" name="Intercom" value="1" {{ old('Intercom', $room->Intercom) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="Intercom">Intercom</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" id="RoomService" name="RoomService" value="1" {{ old('RoomService', $room->RoomService) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="RoomService">Room Service</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" id="Minibar" name="Minibar" value="1" {{ old('Minibar', $room->Minibar) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="Minibar">Minibar</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Update Room
                                </button>
                                <a href="{{ route('rooms.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
