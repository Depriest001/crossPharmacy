@extends('admindashboardLayout')
@section('title','POS | The Cross Pharmacy')
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

        <div id="alertArea"></div>
        <div class="row">
            <!-- Left: Product Search & Add -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light fw-bold py-4">🧾 Point of Sale (POS)</div>
                    <div class="card-body">
                        
                        <!-- Search Bar -->
                        <div class="row mb-3 pt-3">
                            <div class="col-md-8">
                                <label for="barcode" class="form-label fw-semibold">Scan / Enter Product Barcode</label>
                                <input type="text" class="form-control" id="barcode" name="barcode" placeholder="Enter or scan barcode">
                            </div>
                            <div class="col-md-4">
                                <label for="quantity" class="form-label fw-semibold">Quantity</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" min="1" value="1">
                            </div>
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <button class="btn btn-primary" id="addToCartBtn"><i class="bi bi-cart-plus"></i> Add to Cart</button>
                        </div>

                        <!-- Cart Table -->
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                <th>s/n</th>
                                <th>Product</th>
                                <th>Barcode</th>
                                <th>Price (₦)</th>
                                <th>Qty</th>
                                <th>Total (₦)</th>
                                <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="cartTable">
                                <!-- Rows dynamically added via JS -->
                            </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Right: Payment Summary -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light fw-bold py-4">💵 Payment Summary</div>
                    <div class="card-body">
                        <form id="saleForm" method="POST" action="{{ route('sale.store') }}">
                            @csrf

                            <div class="d-flex justify-content-between my-2">
                                <span class="fw-semibold">Subtotal:</span>
                                <span id="subtotal">₦0.00</span>
                                <input type="hidden" name="subtotal" id="subtotalInput" required>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-semibold">Discount:</span>
                                <input type="number" class="form-control form-control-sm w-50 text-end" id="discount" name="discount" value="0">
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between mb-3">
                                <span class="fw-bold fs-5">Grand Total:</span>
                                <span id="grandTotal" class="fw-bold fs-5">₦0.00</span>
                                <input type="hidden" name="grand_total" id="grandTotalInput" required>
                            </div>

                            <label for="payment_method" class="form-label fw-semibold">Payment Method</label>
                            <select id="payment_method" name="payment_method" class="form-select mb-3" required>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="transfer">Transfer</option>
                            </select>

                            <!-- hidden field to hold cart data -->
                            <input type="hidden" name="cart_data" id="cartData" required>

                            <button type="submit" class="btn btn-success w-100" id="completeSaleBtn">
                                <i class="bi bi-check2-circle"></i> Complete Sale
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Optional JS (basic POS logic) -->
<script>
$(document).ready(function () {
    let cart = [];
    const $cartTable = $('#cartTable');
    const $subtotal = $('#subtotal');
    const $grandTotal = $('#grandTotal');
    const $alertArea = $('#alertArea');

    // Hidden inputs
    const $subtotalInput = $('#subtotalInput');
    const $grandTotalInput = $('#grandTotalInput');
    const $cartData = $('#cartData');

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

        // AJAX: Fetch product by barcode
        $.ajax({
            url: `{{ url('admin/product/barcode') }}/${barcode}`,
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                // Check if item already exists in cart
                const existingItem = cart.find(item => item.barcode === data.barcode);
                if (existingItem) {
                    existingItem.qty += qty;
                    existingItem.total = existingItem.price * existingItem.qty;
                } else {
                    cart.push({
                        name: data.name,
                        barcode: data.barcode,
                        price: parseFloat(data.price),
                        qty: qty,
                        total: parseFloat(data.price) * qty
                    });
                }

                renderCart();
                $('#barcode').val('');
            },
            error: function (xhr) {
                if (xhr.status === 404) {
                    showAlert('❌ Product not found. Please check the barcode.', 'danger');
                } else {
                    showAlert('⚠️ Server error. Please try again later.', 'danger');
                }
            }
        });
    });

    // Render cart and update totals
    function renderCart() {
        $cartTable.empty();
        let subtotal = 0;

        $.each(cart, function (index, item) {
            subtotal += item.total;
            $cartTable.append(`
                <tr>
                    <td>${index + 1}</td>
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

        const discount = parseFloat($('#discount').val()) || 0;
        const grandTotal = subtotal - discount;

        // Update UI
        $subtotal.text('₦' + subtotal.toFixed(2));
        $grandTotal.text('₦' + grandTotal.toFixed(2));

        // Update hidden inputs
        $subtotalInput.val(subtotal.toFixed(2));
        $grandTotalInput.val(grandTotal.toFixed(2));
        $cartData.val(JSON.stringify(cart));
    }

    // Remove item
    $(document).on('click', '.removeItem', function () {
        const index = $(this).data('index');
        const removed = cart.splice(index, 1);
        renderCart();
        if (removed.length) {
            showAlert(`🗑️ Removed <strong>${removed[0].name}</strong> from cart.`, 'warning');
        }
    });

    // Update totals when discount changes
    $('#discount').on('input', renderCart);

    // On form submit: ensure cart data is set
    $('#saleForm').on('submit', function (e) {
        if (cart.length === 0) {
            e.preventDefault();
            showAlert('🛒 Please add at least one product to the cart before completing sale.', 'warning');
            return;
        }

        // Update hidden fields again just before submit
        $subtotalInput.val(parseFloat($subtotal.text().replace('₦', '')));
        $grandTotalInput.val(parseFloat($grandTotal.text().replace('₦', '')));
        $cartData.val(JSON.stringify(cart));
    });
});
</script>
@endsection