@extends('admindashboardLayout')
@section('title', 'View Product | The Cross Pharmacy')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0 text-primary">
            <i class="bx bx-capsule me-2"></i> Product Details
        </h4>
        <a href="{{ route('product.index') }}" class="btn btn-outline-primary btn-sm">
            <i class="bx bx-arrow-back me-1"></i> Back to Products
        </a>
    </div>

    <!-- Notifications -->
    @if (session('success') || session('error') || $errors->any())
        <div id="appToast"
            class="bs-toast toast fade show position-fixed bottom-0 end-0 m-3 
            {{ session('success') ? 'bg-success' : (session('error') ? 'bg-danger' : 'bg-warning') }}"
            role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
            <div class="toast-header text-white">
                <i class="bx bx-bell me-2"></i>
                <strong class="me-auto">
                    @if (session('success')) Success 
                    @elseif (session('error')) Error 
                    @else Validation 
                    @endif
                </strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
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

    <!-- Product Details Card -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-light py-3 border-bottom">
            <h5 class="mb-0 fw-semibold text-dark">
                <i class="bx bx-show-alt me-2 text-primary"></i> {{ $product->product_name }}
            </h5>
        </div>

        <div class="card-body px-4 py-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label text-muted fw-semibold">Category</label>
                    <div class="form-control-plaintext fw-bold text-dark">
                        {{ $product->category->name ?? 'No Category' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label text-muted fw-semibold">Brand</label>
                    <div class="form-control-plaintext">{{ $product->brand ?: '—' }}</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label text-muted fw-semibold">Unit</label>
                    <div class="form-control-plaintext">{{ $product->unit }}</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label text-muted fw-semibold">Price per Unit (₦)</label>
                    <div class="form-control-plaintext">₦{{ number_format($product->price, 2) }}</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label text-muted fw-semibold">Quantity in Stock</label>
                    <div class="form-control-plaintext">{{ $product->quantity }}</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label text-muted fw-semibold">Expiry Date</label>
                    <div class="form-control-plaintext">{{ \Carbon\Carbon::parse($product->expiry_date)->format('M d, Y') }}</div>
                </div>

                <div class="col-md-6 d-flex justify-content-between align-items-center">
                    <div>
                        <label class="form-label text-muted fw-semibold">Barcode</label>
                        <div class="text-center" id="barcodeSection">
                            <img 
                                id="barcodeImage" 
                                src="data:image/png;base64,{{ DNS1D::getBarcodePNG($product->barcode, 'C128', 2, 60) }}" 
                                alt="Barcode"
                                width="150px"
                                class="p-2 rounded bg-white"
                            />
                            <small class="d-block text-center mt-1 fw-semibold text-dark">
                                {{ $product->barcode }}
                            </small>
                        </div>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-secondary" id="printBarcode">
                            <i class="bx bx-printer"></i> Print
                        </button>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label text-muted fw-semibold">Status</label>
                    <div>
                        @if ($product->status === 'active')
                            <span class="badge bg-success px-3 py-2 rounded-pill">Active</span>
                        @else
                            <span class="badge bg-danger px-3 py-2 rounded-pill">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer pt-2 bg-light text-end">
            <a href="{{ route('product.edit', $product->id) }}" class="btn btn-primary me-2">
                <i class="bx bx-edit-alt me-1"></i> Edit Product
            </a>
            <form action="{{ route('product.destroy', $product->id) }}" method="POST" class="d-inline-block" 
                  onsubmit="return confirm('Are you sure you want to delete this product?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bx bx-trash me-1"></i> Delete
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('printBarcode').addEventListener('click', function() {
        const printContent = document.getElementById('barcodeSection').innerHTML;
        const printWindow = window.open('', '', 'width=600,height=400');
        printWindow.document.write(`
            <html>
            <head>
                <title>Print Barcode</title>
                <style>
                body { text-align: center; font-family: Arial, sans-serif; margin-top: 20px; }
                img { max-width: 100%; }
                small { display: block; font-weight: bold; margin-top: 10px; }
                </style>
            </head>
            <body>${printContent}</body>
            </html>
        `);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    });
</script>
@endsection