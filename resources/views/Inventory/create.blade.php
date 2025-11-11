@extends('layouts.app')
@section('content')
<div class="container-fluid py-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-defult">
                    <h2 class="card-title"><i class="fa-solid fa-plus-circle mr-2"></i>Add New Inventory Item</h2>
                </div>
                <div class="card-body">
                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('inventory.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>SKU *</label>
                                    <input type="text" name="sku" class="form-control" value="{{ old('sku') }}" required>
                                    <small class="text-muted">Unique stock keeping unit</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Name *</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Category *</label>
                                    <input type="text" name="category" class="form-control" value="{{ old('category') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Brand</label>
                                    <input type="text" name="brand" class="form-control" value="{{ old('brand') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Cost Price ($) *</label>
                                    <input type="number" name="cost_price" class="form-control" step="0.01" min="0" value="{{ old('cost_price') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Selling Price ($) *</label>
                                    <input type="number" name="selling_price" class="form-control" step="0.01" min="0" value="{{ old('selling_price') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Quantity *</label>
                                    <input type="number" name="quantity" class="form-control" min="0" value="{{ old('quantity', 0) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Minimum Stock Level *</label>
                                    <input type="number" name="min_stock_level" class="form-control" min="0" value="{{ old('min_stock_level', 0) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Maximum Stock Level</label>
                                    <input type="number" name="max_stock_level" class="form-control" min="0" value="{{ old('max_stock_level') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Location</label>
                                    <input type="text" name="location" class="form-control" value="{{ old('location') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Barcode</label>
                                    <input type="text" name="barcode" class="form-control" value="{{ old('barcode') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Supplier</label>
                                    <input type="text" name="supplier" class="form-control" value="{{ old('supplier') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Supplier Contact</label>
                                    <input type="text" name="supplier_contact" class="form-control" value="{{ old('supplier_contact') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Expiry Date</label>
                                    <input type="date" name="expiry_date" class="form-control" value="{{ old('expiry_date') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status *</label>
                                    <select name="status" class="form-control" required>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="discontinued" {{ old('status') == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Item Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small class="text-muted">Accepted formats: JPEG, PNG, JPG, GIF. Max size: 2MB.</small>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn bg-navy">
                                <i class="fa fa-save mr-1"></i>Create Item
                            </button>
                            <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                                <i class="fa fa-times mr-1"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
