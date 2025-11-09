<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt | The Cross Pharmacy</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/favicon.jpg') }}" />

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f3f6f9;
            font-family: "Segoe UI", Roboto, sans-serif;
        }

        .receipt-wrapper {
            max-width: 700px;
            margin: 3rem auto;
        }

        .receipt-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            padding: 2.5rem;
        }

        .receipt-header {
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }

        .receipt-header img {
            max-width: 160px;
            margin-bottom: 0.5rem;
        }

        .receipt-header h4 {
            font-weight: 700;
            color: #343a40;
        }

        .receipt-meta p {
            margin: 0;
            font-size: 0.9rem;
            color: #555;
        }

        table {
            font-size: 0.9rem;
        }

        .table thead {
            background-color: #f8f9fa;
        }

        .table tbody tr:nth-child(even) {
            background-color: #fcfcfc;
        }

        .totals p, .totals h5 {
            margin: 0;
            font-size: 0.95rem;
        }

        .totals h5 {
            font-size: 1.1rem;
            margin-top: 0.5rem;
        }

        .receipt-footer {
            border-top: 2px solid #e9ecef;
            margin-top: 2rem;
            padding-top: 1rem;
            text-align: center;
            color: #6c757d;
            font-size: 0.9rem;
        }

        @media print {
            @page {
                margin: 0;
            }
            body {
                background: #fff;
                -webkit-print-color-adjust: exact;
            }
            .btn, a {
                display: none !important;
            }
            .receipt-card {
                box-shadow: none;
                border: none;
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container receipt-wrapper">
        <div class="receipt-card">

            <!-- HEADER -->
            <div class="receipt-header text-center">
                <img src="{{ asset('assets/images/logo1.jpg') }}" alt="Logo">
                <h4 class="fw-bold mt-2">{{ $systemInfo->system_name ?? 'N/A' }}</h4>
                <p class="text-muted small mb-0">{{ $systemInfo->address }}</p>
                <p class="text-muted small">📞 {{ $systemInfo->phone ?? 'N/A' }} | ✉️ {{ $systemInfo->email ?? 'N/A' }}</p>
            </div>

            <!-- META INFO -->
            <div class="row mb-3 receipt-meta">
                <div class="col-6">
                    <p><strong>Date:</strong> {{ $sale->created_at->format('d M, Y h:i A') }}</p>
                </div>
                <div class="col-6 text-end">
                    <p><strong>Payment Method:</strong> {{ ucfirst($sale->payment_method) }}</p>
                </div>
            </div>

            <!-- TABLE -->
            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price (₦)</th>
                            <th>Total (₦)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="text-start">{{ $item->product->product_name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->price, 2) }}</td>
                            <td>{{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- TOTALS -->
            <div class="totals text-end mt-3">
                <p><strong>Subtotal:</strong> ₦{{ number_format($sale->subtotal, 2) }}</p>
                <p><strong>Discount:</strong> ₦{{ number_format($sale->discount, 2) }}</p>
                <h6 class="fw-bold text-dark"><strong>Grand Total:</strong> ₦{{ number_format($sale->grand_total, 2) }}</h6>
            </div>

            <!-- BUTTONS -->
            <div class="text-center mt-4 d-flex flex-column flex-sm-row justify-content-center gap-3">
                <button onclick="window.print()" class="btn btn-dark px-4">
                    🖨️ Print Receipt
                </button>
                <a href="{{ route('sale.index') }}" class="btn btn-outline-secondary px-4">
                    Back to POS
                </a>
            </div>

            <!-- FOOTER -->
            <div class="receipt-footer mt-4">
                <p>Thank you for your patronage 💚</p>
                <p><em>“Your Health, Our Priority.”</em></p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (optional for print buttons) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>