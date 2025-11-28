@extends('admindashboardLayout')
@section('title', 'Sales Report | The Cross Pharmacy')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4 report-header">
        <h4 class="fw-bold mb-0">Sales Report</h4>
        <div>
            <!-- <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
                <i class="bx bx-printer"></i> Print Report
            </button> -->
            <button type="button" id="printTableBtn" class="btn btn-sm btn-outline-secondary">
                <i class="bx bx-printer"></i> Print Report
            </button>
        </div>
    </div>

    <!-- FILTERS CARD -->
    <div class="card mb-4">
        <div class="card-body">
                        
            <form method="GET" action="{{ route('report.sale') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" required />
                </div>

                <div class="col-md-4">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>

                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bx bx-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- REPORT TABLE -->
    <div class="card">
        <div class="card-body table-responsive text-nowrap">
            <table class="table table-sm table-striped table-hover align-middle" id="exampleTable">
                <thead class="table-success">
                    <tr>
                        <th>S/N</th>
                        <th>Date</th>

                        @if($role === 'Admin')
                            <th>Staff</th>
                            <th>Branch</th>

                        @elseif($role === 'Staff')
                            <th>Staff</th>
                        @endif
                        <th>Status</th>
                        <th>Total Items Sold</th>
                        <th>Total Amount (₦)</th>
                        <th class="no-print">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $index => $sale)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $sale->created_at->format('d M, Y h:i A') }}</td>
                            @if($role === 'Admin')
                                <td>{{ $sale->staff->full_name ?? 'N/A' }}</td>
                                <td>{{ $sale->staff->branch->name ?? 'N/A' }}</td>
                            @elseif($role === 'Staff')
                                <td>{{ $sale->staff->full_name ?? 'N/A' }}</td>
                            @endif
                            <td>
                                @if ($sale->status === 'pending')
                                    <span class="badge bg-label-warning">Pending</span>
                                @elseif ($sale->status === 'completed')
                                    <span class="badge bg-label-success">Completed</span>
                                @else
                                    <span class="badge bg-label-danger">Cancelled</span>
                                @endif
                            </td>
                            <td>{{ $sale->items_sum_quantity ?? 0 }}</td>
                            <td>₦{{ number_format($sale->grand_total, 2) }}</td>
                            <td class="no-print">
                                <a class="btn btn-sm p-1 btn-info" href="{{ route('admin.sale.receipt', $sale->id) }}"><i class="icon-base bx bx-show-alt"></i></a>
                            </td>
                        </tr>
                    @empty
                    @endforelse
                </tbody>
                @if(count($sales) > 0)
                    <tfoot class="table-light fw-bold">
                        <tr>
                            @if($role === 'Admin')
                                <td colspan="5" class="text-end">Total:</td>
                            @elseif($role === 'Staff')
                                <td colspan="4" class="text-end">Total:</td>
                            @else
                                <td colspan="3" class="text-end">Total:</td>
                            @endif
                            <td>{{ $sales->sum('items_sum_quantity') }}</td>
                            <td colspan="3">₦{{ number_format($sales->sum('grand_total'), 2) }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
<script>
document.getElementById('printTableBtn').addEventListener('click', function () {
    // Initialize DataTable instance
    var table = $('#exampleTable').DataTable();

    // Get all rows (even those not visible due to pagination)
    var allRows = table.rows({ search: 'applied' }).nodes().to$();

    // Clone the header and footer
    var thead = $('#exampleTable thead').clone();
    var tfoot = $('#exampleTable tfoot').clone();

    // Build a new table for printing
    var printTable = $('<table></table>')
        .addClass('table table-sm table-striped table-hover')
        .append(thead)
        .append(allRows.clone())  // clone to avoid affecting original table
        .append(tfoot);

    // Remove Actions column and barcodes for print
    printTable.find('th.no-print, td.no-print').remove();

    // Open print window
    var printWindow = window.open('', '', 'width=1200,height=800');
    printWindow.document.write(`
        <html>
            <head>
                <title>Staff Table - Print</title>
                <style>
                    @page { size: A4 landscape; margin: 10mm; }
                    body { font-family: Arial, sans-serif; padding: 20px; }
                    table { width: 100%; border-collapse: collapse; font-size: 12px; }
                    th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
                    th { background: #d1f7d1; }
                    tfoot td { font-weight: bold; background: #f2f2f2; }
                    h4 { text-align: center; margin-bottom: 20px; }
                </style>
            </head>
            <body>
                <h4>Staff List</h4>
                <p><strong>Printed by:</strong> {{ auth('staff')->user()->full_name ?? 'N/A' }}</p>
                ${printTable.prop('outerHTML')}
            </body>
        </html>
    `);

    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
});
</script>
@endsection