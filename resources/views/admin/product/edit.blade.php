@extends('admindashboardLayout')
@section('title','Edit Product | The Cross Pharmacy')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="col-md-8 mx-auto card">
            <div class="card-header bg-light py-3">
                <h5>Edit Product</h5>
            </div>

            @if (session('success') || session('error') || $errors->any())
            <div id="appToast"
                class="bs-toast toast fade show position-fixed bottom-0 end-0 m-3
                {{ session('success') ? 'bg-success' : (session('error') ? 'bg-danger' : 'bg-warning') }}"
                role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
                <div class="toast-header text-white">
                    <i class="icon-base bx bx-bell me-2"></i>
                    <div class="me-auto fw-medium">
                    @if (session('success'))
                        Success
                    @elseif (session('error'))
                        Error
                    @else
                        Validation
                    @endif
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>

                <div class="toast-body text-white">
                    @if (session('success'))
                    {{ session('success') }}
                    @elseif (session('error'))
                    {{ session('error') }}
                    @elseif ($errors->any())
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
            @endif
            
            <div class="card-body">
                <form id="formAccountSettings" action="{{ route('product.update', $product->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-6">
                        <div class="col-md-12">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="product_name" class="form-control"
                            value="{{ $product->product_name }}" autofocus required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select" required>
                                <option value="" selected disabled>Select Category</option>
                                @foreach ($categories as $category)
                                    <option 
                                        value="{{ $category->id }}" 
                                        {{ (int)$product->category_id === (int)$category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Brand</label>
                            <input type="text" name="brand" class="form-control"
                            value="{{ $product->brand }}" placeholder="e.g., Emzor">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Unit</label>
                            <input type="text" name="unit" class="form-control"
                            value="{{ $product->unit }}" placeholder="e.g., Pack, Bottle" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Price per Unit (₦)</label>
                            <input type="number" step="0.01" name="price" class="form-control"
                            value="{{ $product->price }}" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Quantity in Stock</label>
                            <input type="number" name="quantity" class="form-control"
                            value="{{ $product->quantity }}" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Expiry Date</label>
                            <input type="date" name="expiry_date" class="form-control"
                            value="{{ $product->expiry_date }}" required>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="active" {{ $product->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $product->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="btn btn-primary me-3">Save</button>
                        <a href="{{ route('product.index')}}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection