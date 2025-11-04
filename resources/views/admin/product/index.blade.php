@extends('admindashboardLayout')
@section('title','Manage Products | The Cross Pharmacy')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header py-3 d-flex justify-content-between">
                <h5>Products Table</h5>
                <div class="dt-buttons btn-group flex-wrap mb-0">
                    <div class="btn-group">
                        <button class="btn btn-sm btn-secondary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBackdrop" aria-controls="offcanvasBackdrop">
                            <i class="icon-base bx bx-plus icon-md"></i>
                            <span class="d-none d-sm-inline-block">Add</span>
                        </button>
                        <div class="btn-group" id="dropdown-icon-demo">
                            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="icon-base bx bx-export me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Export</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ route('export.data', ['table' => 'products', 'type' => 'print']) }}" target="_blank"
                                    class="dropdown-item d-flex align-items-center">
                                        <i class="icon-base bx bx-printer me-1"></i>Print
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('export.data', ['table' => 'products', 'type' => 'csv']) }}"
                                    class="dropdown-item d-flex align-items-center">
                                        <i class="icon-base bx bx-file me-1"></i>CSV
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('export.data', ['table' => 'products', 'type' => 'excel']) }}"
                                    class="dropdown-item d-flex align-items-center">
                                        <i class="icon-base bx bxs-file-export me-1"></i>Excel
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('export.data', ['table' => 'products', 'type' => 'pdf']) }}"
                                    class="dropdown-item d-flex align-items-center">
                                        <i class="icon-base bx bxs-file-pdf me-1"></i>PDF
                                    </a>
                                </li>
                            </ul>

                        </div>
                    </div>
                </div>
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

            <div class="table-responsive text-nowrap">
                <table class="table table-sm table-striped table-hover" id="exampleTable">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Barcode</th>
                            <th>Name</th>
                            <th>Price (₦)</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody class="table-border-bottom-0">
                        @forelse ($products as $product)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {!! DNS1D::getBarcodeHTML($product->barcode, 'C128', 1.2, 30) !!}
                                    <small class="d-block">{{ $product->barcode }}</small>
                                </td>
                                <td>{{ $product->product_name }}</td>
                                <td>₦{{ number_format($product->price, 2) }}</td>
                                <td>{{ \Carbon\Carbon::parse($product->expiry_date)->format('M d, Y') }}</td>
                                <td>
                                    @if ($product->status === 'active')
                                        <span class="badge bg-label-success">Active</span>
                                    @else
                                        <span class="badge bg-label-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a class="btn btn-sm p-1 btn-primary me-1" href="{{ route('product.edit', $product->id) }}"><i class="icon-base bx bx-edit-alt"></i></a>
                                    <a class="btn btn-sm p-1 btn-info me-1" href="{{ route('product.show', $product->id) }}"><i class="icon-base bx bx-show-alt"></i></a>
                                    <!-- Delete Button -->
                                    <form action="{{ route('product.destroy', $product->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Are you sure you want to delete this product ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm p-1 btn-danger">
                                            <i class="icon-base bx bx-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add new product Modal -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasBackdrop" aria-labelledby="offcanvasBackdropLabel">
        <div class="offcanvas-header">
            <h5 id="offcanvasBackdropLabel" class="offcanvas-title">Add New Product</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body my-auto mx-0 flex-grow-0">
            <form id="formAccountSettings" action="{{ route('product.store') }}" method="POST">
                @csrf
                <div class="row g-6">
                    <div class="col-md-12">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="product_name" class="form-control" autofocus required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select" required>
                            <option value="" selected disabled>Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Brand</label>
                        <input type="text" name="brand" class="form-control" placeholder="e.g., Emzor">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Unit</label>
                        <input type="text" name="unit" class="form-control" placeholder="e.g., Pack, Bottle" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Price per Unit (₦)</label>
                        <input type="number" step="0.01" name="price" class="form-control" required>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Quantity in Stock</label>
                        <input type="number" name="quantity" class="form-control" required>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Expiry Date</label>
                        <input type="date" name="expiry_date" class="form-control" required>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="btn btn-primary me-3">Save</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </div>
            </form>
        </div>
    </div>
@endsection