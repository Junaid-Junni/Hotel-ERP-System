@extends('layouts.app')

@section('title', 'Edit Room')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Room - {{ $room->RoomNo }}</h3>
                    <a href="{{ route('rooms.index') }}" class="btn btn-secondary float-right">
                        <i class="fa fa-arrow-left"></i> Back to Rooms
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('rooms.update', $room->id) }}" method="POST" enctype="multipart/form-data" id="roomForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="RoomNo">Room Number *</label>
                                    <input type="text" class="form-control @error('RoomNo') is-invalid @enderror"
                                           id="RoomNo" name="RoomNo" value="{{ old('RoomNo', $room->RoomNo) }}" required>
                                    @error('RoomNo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="Floor">Floor *</label>
                                    <input type="number" class="form-control @error('Floor') is-invalid @enderror"
                                           id="Floor" name="Floor" value="{{ old('Floor', $room->Floor) }}" min="1" required>
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
                                        <option value="Executive" {{ old('Type', $room->Type) == 'Executive' ? 'selected' : '' }}>Executive</option>
                                        <option value="Presidential" {{ old('Type', $room->Type) == 'Presidential' ? 'selected' : '' }}>Presidential</option>
                                    </select>
                                    @error('Type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="Price">Price ($) *</label>
                                    <input type="number" step="0.01" class="form-control @error('Price') is-invalid @enderror"
                                           id="Price" name="Price" value="{{ old('Price', $room->Price) }}" min="0" required>
                                    @error('Price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="Capacity">Capacity (Persons) *</label>
                                    <input type="number" class="form-control @error('Capacity') is-invalid @enderror"
                                           id="Capacity" name="Capacity" value="{{ old('Capacity', $room->Capacity) }}" min="1" required>
                                    @error('Capacity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
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

                                <!-- Existing Images -->
                                @if($room->Images && count($room->Images) > 0)
                                <div class="form-group">
                                    <label>Existing Images</label>
                                    <div class="row">
                                        @foreach($room->Images as $image)
                                        <div class="col-md-3 mb-2">
                                            <img src="{{ Storage::url($image) }}" class="img-fluid rounded" alt="Room Image" style="max-height: 100px; object-fit: cover;">
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                <div class="form-group">
                                    <label>Add More Images</label>
                                    <input type="file" class="form-control-file" id="images" name="images[]" multiple accept="image/*">
                                    <small class="form-text text-muted">You can select multiple images</small>
                                </div>

                                <div class="form-group">
                                    <label>Amenities</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="AC" name="AC" value="1" {{ old('AC', $room->AC) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="AC">Air Conditioning</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="WiFi" name="WiFi" value="1" {{ old('WiFi', $room->WiFi) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="WiFi">WiFi</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="TV" name="TV" value="1" {{ old('TV', $room->TV) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="TV">TV</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="Geyser" name="Geyser" value="1" {{ old('Geyser', $room->Geyser) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="Geyser">Geyser</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="Balcony" name="Balcony" value="1" {{ old('Balcony', $room->Balcony) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="Balcony">Balcony</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="Intercom" name="Intercom" value="1" {{ old('Intercom', $room->Intercom) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="Intercom">Intercom</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="RoomService" name="RoomService" value="1" {{ old('RoomService', $room->RoomService) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="RoomService">Room Service</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="Minibar" name="Minibar" value="1" {{ old('Minibar', $room->Minibar) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="Minibar">Minibar</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Update Room
                            </button>
                            <a href="{{ route('rooms.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
