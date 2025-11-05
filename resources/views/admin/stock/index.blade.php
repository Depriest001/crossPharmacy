@extends('admindashboardLayout')
@section('title','Stock Level Tracking | The Cross Pharmacy')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <!-- Available Stock -->
                    <div class="col-md-4 col-12 mb-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between mb-2">
                                    <span class="fw-semibold text-success">
                                        <i class="bx bx-package me-1"></i> Available Stock
                                    </span>
                                </div>
                                <h4 class="fw-bold mb-0">{{ number_format($availableStock) }}</h4>
                                <small class="text-muted">Active, non-expired items</small>
                            </div>
                        </div>
                    </div>

                    <!-- Expired Stock -->
                    <div class="col-md-4 col-12 mb-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between mb-2">
                                    <span class="fw-semibold text-danger">
                                        <i class="bx bx-error-circle me-1"></i> Expired Stock
                                    </span>
                                </div>
                                <h4 class="fw-bold mb-0">{{ number_format($expiredStock) }}</h4>
                                <small class="text-muted">Products past expiry date</small>
                            </div>
                        </div>
                    </div>

                    <!-- Low Stock -->
                    <div class="col-md-4 col-12 mb-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between mb-2">
                                    <span class="fw-semibold text-warning">
                                        <i class="bx bx-down-arrow-alt me-1"></i> Low Stock
                                    </span>
                                </div>
                                <h4 class="fw-bold mb-0">{{ number_format($lowStockCount) }}</h4>
                                <small class="text-muted">Products ≤ 10 units left</small>
                            </div>
                        </div>
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

        @php
            $role = strtolower(auth('staff')->user()->role->role_type ?? '');
        @endphp
        @if(in_array($role, ['admin', 'staff']))
        <div class="row px-2">
            <div class="card p-0">
                <div class="card-header bg-light py-2">
                    <h5>Low Stock Table</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-sm table-striped table-hover" id="exampleTable">
                            <thead>
                                <tr>
                                <th>S/n</th>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Quantity</th>
                                <th>Expiry Date</th>
                                <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @foreach($products as $product)
                                <tr
                                    @if($product->quantity == 0)
                                        class="table-danger"
                                    @elseif($product->quantity < 10)
                                        class="table-warning"
                                    @endif
                                >
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $product->product_name }}</td>
                                    <td>{{ $product->category->name ?? 'N/A' }}</td>
                                    <td>{{ $product->quantity }}</td>
                                    <td>{{ \Carbon\Carbon::parse($product->expiry_date)->format('d M, Y') }}</td>
                                    <td>
                                        <a class="btn btn-sm p-1 btn-info me-1" href="{{ route('product.show', $product->id) }}"><i class="icon-base bx bx-show-alt"></i></a>
                                        <button class="btn btn-sm btn-outline-primary open-adjust"
                                                data-bs-toggle="offcanvas"
                                                data-bs-target="#adjustOffcanvas"
                                                data-product-id="{{ $product->id }}"
                                                data-product-name="{{ $product->product_name }}"
                                                data-expiry="{{ $product->expiry_date ? \Carbon\Carbon::parse($product->expiry_date)->format('Y-m-d') : '' }}">
                                        Adjust
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Offcanvas for {{ $product->product_name }} -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="adjustOffcanvas" aria-labelledby="adjustOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 id="adjustOffcanvasLabel" class="offcanvas-title">Adjust</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body my-auto mx-0 flex-grow-0">
                <form action="{{ route('stock.adjust.store') }}" method="POST" id="adjustForm">
                    @csrf
                    <input type="hidden" name="product_id" id="product_id">

                    <div class="mb-3">
                        <label class="form-label">Adjustment Type</label>
                        <select name="adjust_type" class="form-select" id="adjust_type" required>
                        <option value="">Select Type</option>
                        <option value="add">Add Stock</option>
                        <option value="remove">Remove Stock</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="quantity" class="form-control" placeholder="Enter quantity" min="1" required>
                    </div>

                    <div class="mb-3" id="expiry_wrap" style="display:none;">
                        <label class="form-label">Expiry Date</label>
                        <input type="date" name="expiry_date" class="form-control" id="expiry_input">
                        <small class="text-muted">Specify expiry date for the new stock.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reason (optional)</label>
                        <textarea name="reason" class="form-control" rows="2" placeholder="E.g., expired products, restock, correction..."></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>
    
<script>
document.addEventListener('DOMContentLoaded', function () {
  const offcanvasEl = document.getElementById('adjustOffcanvas');
  const form = document.getElementById('adjustForm');
  const title = document.getElementById('adjustOffcanvasLabel');
  const productIdInput = document.getElementById('product_id');
  const adjustType = document.getElementById('adjust_type');
  const expiryWrap = document.getElementById('expiry_wrap');
  const expiryInput = document.getElementById('expiry_input');

  // When "Adjust" is clicked, fill the offcanvas with the product data
  document.body.addEventListener('click', function (e) {
    const btn = e.target.closest('.open-adjust');
    if (!btn) return;

    const pid = btn.getAttribute('data-product-id');
    const name = btn.getAttribute('data-product-name') || 'Item';
    const expiry = btn.getAttribute('data-expiry') || '';

    title.textContent = 'Adjust ' + name;
    productIdInput.value = pid;
    expiryInput.value = expiry;

    // reset state each open
    adjustType.value = '';
    expiryWrap.style.display = 'none';
    expiryInput.removeAttribute('required');
  });

  // Toggle expiry when adjustment type changes
  adjustType.addEventListener('change', function () {
    if (this.value === 'add') {
      expiryWrap.style.display = 'block';
      expiryInput.setAttribute('required', 'required');
    } else {
      expiryWrap.style.display = 'none';
      expiryInput.removeAttribute('required');
    }
  });
});
</script>

@endsection