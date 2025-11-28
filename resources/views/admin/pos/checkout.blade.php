@extends('admindashboardLayout')
@section('title','POS Checkout | The Cross Pharmacy')

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

    <div class="card shadow-sm border-0">
        <div class="card-header bg-light fw-bold py-4">🛒 Pending Sales</div>
        <div class="card-body pt-3">

            <div class="table-responsive">
                <table class="table table-sm table-bordered" id="exampleTable">
                    <thead class="table-light">
                        <tr>
                            <th>Sale ID</th>
                            <th>Seller</th>
                            <th>Subtotal</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingSales as $sale)
                        <tr>
                            <td>{{ $sale->id }}</td>
                            <td>{{ $sale->seller->full_name ?? 'N/A' }}</td>
                            <td>₦{{ number_format($sale->subtotal,2) }}</td>
                            <td>{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary viewSaleBtn" data-id="{{ $sale->id }}">
                                    View
                                </button>
                            </td>
                        </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<!-- Modal: Sale Details -->
<div class="modal fade" id="saleModal" tabindex="-1" aria-labelledby="saleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="saleModalLabel">Sale Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="saleItemsTable" class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Price (₦)</th>
                                <th>Total (₦)</th>
                            </tr>
                        </thead>
                        <tbody id="saleItemsBody"></tbody>
                    </table>
                </div>

                <form id="completeSaleForm" method="POST" action="{{ route('checkout.complete') }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="sale_id" id="saleIdInput">
                    <div class="mb-3">
                        <label for="discount" class="form-label">Discount (₦)</label>
                        <input type="number" class="form-control" name="discount" id="discount" value="0" min="0">
                    </div>
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select class="form-select" name="payment_method" id="payment_method" required>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-between fw-bold mb-3">
                        <span>Grand Total:</span>
                        <span id="grandTotalDisplay">₦0.00</span>
                    </div>
                    <input type="hidden" name="grand_total" id="grandTotalInput">
                    <button type="submit" class="btn btn-success w-100">✅ Complete Sale</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JS -->
<!-- <script>
$(document).ready(function () {
    const $alertArea = $('#alertArea');

    function showAlert(message, type = 'success') {
        $alertArea.html(`
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `);
        setTimeout(() => $('.alert').alert('close'), 5000);
    }

    // Open sale details modal
    $('.viewSaleBtn').on('click', function () {
        const saleId = $(this).data('id');
        $('#saleItemsBody').empty();
        $('#saleIdInput').val(saleId);

        $.ajax({
            url: `{{ url('admin/checkout') }}/${saleId}`,
            method: 'GET',
            success: function(res) {
                const items = res.items;
                let grandTotal = parseFloat(res.sale.subtotal);

                items.forEach((item, index) => {
                    $('#saleItemsBody').append(`
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.name}</td>
                            <td>${item.quantity}</td>
                            <td>${parseFloat(item.price).toFixed(2)}</td>
                            <td>${parseFloat(item.total).toFixed(2)}</td>
                        </tr>
                    `);
                });

                $('#grandTotalDisplay').text('₦' + grandTotal.toFixed(2));
                $('#grandTotalInput').val(grandTotal.toFixed(2));
                $('#saleModal').modal('show');
            },
            error: function(xhr) {
                showAlert('❌ Unable to fetch sale details.', 'danger');
            }
        });
    });

    // Update grand total if discount changes
    $('#discount').on('input', function () {
        const discount = parseFloat($(this).val()) || 0;
        let total = parseFloat($('#grandTotalInput').val());
        let newTotal = total - discount;
        if(newTotal < 0) newTotal = 0;
        $('#grandTotalDisplay').text('₦' + newTotal.toFixed(2));
        $('#grandTotalInput').val(newTotal.toFixed(2));
    });

    // Optionally: handle AJAX submit for complete sale form
});
</script> -->
<!-- JS -->
<script>
$(document).ready(function () {
    const $alertArea = $('#alertArea');

    function showAlert(message, type = 'success') {
        $alertArea.html(`
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `);
        setTimeout(() => $('.alert').alert('close'), 5000);
    }

    // Cache commonly used elements
    const $saleItemsBody = $('#saleItemsBody');
    const $grandTotalDisplay = $('#grandTotalDisplay');
    const $grandTotalInput = $('#grandTotalInput'); // hidden input used for form submit
    const $discount = $('#discount');
    const $saleModal = $('#saleModal');

    // Keep original total in closure (reset every time modal opens)
    let originalTotal = 0;

    // Safe number parsing helper
    function toNumber(val) {
        const n = parseFloat(String(val).replace(/,/g, ''));
        return Number.isFinite(n) ? n : 0;
    }

    // Recalculate total from originalTotal and discount, update display + hidden input
    function recalcTotal() {
        const discountVal = toNumber($discount.val());
        let newTotal = originalTotal - discountVal;
        if (newTotal < 0) newTotal = 0;
        const fixed = newTotal.toFixed(2);
        $grandTotalDisplay.text('₦' + fixed);
        $grandTotalInput.val(fixed);
    }

    // Open sale details modal
    $('.viewSaleBtn').off('click').on('click', function () {
        const saleId = $(this).data('id');
        $saleItemsBody.empty();
        $('#saleIdInput').val(saleId);

        $.ajax({
            url: `{{ url('admin/checkout') }}/${saleId}`,
            method: 'GET',
            success: function(res) {
                const items = res.items || [];
                // populate items
                items.forEach((item, index) => {
                    $saleItemsBody.append(`
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.name}</td>
                            <td>${toNumber(item.quantity)}</td>
                            <td>${toNumber(item.price).toFixed(2)}</td>
                            <td>${(toNumber(item.total)).toFixed(2)}</td>
                        </tr>
                    `);
                });

                // Set original total from server (use subtotal or grand_total depending on your response)
                originalTotal = toNumber(res.sale.subtotal ?? res.sale.grand_total ?? 0);
                // Initialize display and hidden input
                $grandTotalDisplay.text('₦' + originalTotal.toFixed(2));
                $grandTotalInput.val(originalTotal.toFixed(2));

                // Reset discount and ensure calculation is bound
                $discount.val(0);
                recalcTotal();

                // Show modal
                $saleModal.modal('show');
            },
            error: function(xhr) {
                showAlert('❌ Unable to fetch sale details.', 'danger');
            }
        });
    });

    // Recalculate on discount input change (input and change to catch paste/autofill)
    $discount.off('input change').on('input change', function () {
        recalcTotal();
    });

    // Ensure recalc runs when modal shown (extra safety)
    $saleModal.on('shown.bs.modal', function () {
        recalcTotal();
    });

    // Before submit ensure hidden grand_total matches display (safety)
    $('#completeSaleForm').on('submit', function () {
        recalcTotal();
        return true;
    });
});
</script>
@endsection
