@extends('admindashboardLayout')
@section('title','POS Entry | The Cross Pharmacy')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

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

    <div class="row">
        <!-- Left: Product Search & Add -->
        <div class="col-lg-8 mb-4 order-md-1 order-2">
            <div id="alertArea"></div>
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light fw-bold py-4">🧾 POS Entry (Seller)</div>
                <div class="card-body">

                    <!-- Barcode & Quantity -->
                    <div class="row mb-3 pt-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Scan / Enter Product Barcode</label>
                            <input type="text" id="barcode" class="form-control" placeholder="Enter or scan barcode">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Quantity</label>
                            <input type="number" id="quantity" class="form-control" min="1" value="1">
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button id="addToCartBtn" class="btn btn-primary">
                            <i class="bi bi-cart-plus"></i> Add to Cart
                        </button>
                    </div>

                    <!-- Live Cart -->
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>s/n</th>
                                    <th>Product</th>
                                    <th>Barcode</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="cartTable"></tbody>
                        </table>
                    </div>

                    <!-- Send to Cashier -->
                    <form id="sendToCashierForm" method="POST" action="{{ route('pos.entry.save') }}">
                        @csrf
                        <input type="hidden" name="cart_data" id="cartData">
                        <input type="hidden" name="subtotal" id="subtotalInput">
                        <button type="submit" class="btn btn-success w-100 mt-2">🚀 Send to Cashier</button>
                    </form>

                    <!-- Pending Sales List -->
                    <hr>
                    <h5 class="mt-4">🕒 Pending Sales</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Sale ID</th>
                                    <th>Subtotal</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingSales as $sale)
                                <tr>
                                    <td>{{ $sale->id }}</td>
                                    <td>₦{{ number_format($sale->subtotal,2) }}</td>
                                    <td>{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <!-- Update Sale  -->
                                        <form action="{{ route('pos.pending.destroy', $sale->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to cancelled this pending sale?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Cancelled Sale">
                                                <i class="bx bx-x"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                                @if(count($pendingSales) == 0)
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No pending sales</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        <!-- Right: Subtotal -->
        <div class="col-lg-4 mb-md-0 mb-4 order-md-2 order-1">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light fw-bold py-4">📦 Summary</div>
                <div class="card-body">
                    <div class="d-flex justify-content-between my-2">
                        <span class="fw-semibold">Subtotal:</span>
                        <span id="subtotal">₦0.00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS -->
<script>
$(document).ready(function () {
    let cart = [];

    const $cartTable = $('#cartTable');
    const $subtotal = $('#subtotal');
    const $subtotalInput = $('#subtotalInput');
    const $cartData = $('#cartData');
    const $alertArea = $('#alertArea');

    // Utility: show alert messages
    function showAlert(message, type = 'danger') {
        $alertArea.html(`
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `);
        setTimeout(() => $('.alert').alert('close'), 4000);
    }

    // Add to Cart
    $('#addToCartBtn').on('click', function () {
        const barcode = $('#barcode').val().trim();
        const qty = parseInt($('#quantity').val());

        if (!barcode || qty < 1) {
            showAlert('⚠️ Please enter a valid barcode and quantity.', 'warning');
            return;
        }

        $.ajax({
            url: `{{ url('admin/product/barcode') }}/${barcode}`,
            method: 'GET',
            success: function (data) {
                const existingItem = cart.find(item => item.barcode === data.barcode);

                if (existingItem) {
                    const newQty = existingItem.qty += qty;
                    if (newQty <= data.quantity) {
                        existingItem.qty = newQty;
                        existingItem.total = existingItem.price * existingItem.qty;
                    }else{
                        showAlert('⚠️ Error. Available Stock is low than Purchase Quantity.', 'danger');
                    }
                    
                } else {
                    if (qty <= data.quantity) {
                        cart.push({
                            name: data.name,
                            barcode: data.barcode,
                            price: parseFloat(data.price),
                            qty: qty,
                            total: parseFloat(data.price) * qty
                        });
                    }else{
                        showAlert('⚠️ Error. Available Stock is low than Purchase Quantity.', 'danger');
                    }
                }

                renderCart();
                $('#barcode').val('');
            },
            error: function(xhr, status, error) {
                if (xhr.status === 404) {
                    showAlert('❌ Product not found. Please check the barcode.', 'danger');
                } else {
                    showAlert(xhr.status + '⚠️ Server error. Please try again later.', 'danger');
                }
            }
        });
    });

    // Render Cart
    function renderCart() {
        $cartTable.empty();
        let subtotal = 0;

        cart.forEach((item, index) => {
            subtotal += item.total;
            $cartTable.append(`
                <tr>
                    <td>${index+1}</td>
                    <td>${item.name}</td>
                    <td>${item.barcode}</td>
                    <td>${item.price.toFixed(2)}</td>
                    <td>${item.qty}</td>
                    <td>${item.total.toFixed(2)}</td>
                    <td>
                        <button class="btn btn-sm btn-danger removeItem" data-index="${index}">
                            <i class="bx bx-trash"></i>
                        </button>
                    </td>
                </tr>
            `);
        });

        $subtotal.text('₦' + subtotal.toFixed(2));
        $subtotalInput.val(subtotal);
        $cartData.val(JSON.stringify(cart));
    }

    // Remove item
    $(document).on('click', '.removeItem', function () {
        const index = $(this).data('index');
        cart.splice(index, 1);
        renderCart();
    });

});
</script>
@endsection
