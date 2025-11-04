
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ucfirst($table)}} Print View</title>    
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css')}}" />
</head>
<body>
<style>
    .table thead tr th {
        padding: 10px !important;
    }
    .table tbody tr td {
        padding: 10px !important;
    }
    @media print{
        @page {
            margin: 10px;
            layout: landscape;
        }
    }
</style>
<div class="container py-4">
    <h3 class="mb-3 text-center">{{ ucfirst($table) }} Table</h3>
    <div class="table-responsive">
        <table class="table table-sm table-bordered table-striped">
            <thead class="table-light p-0">
                <tr>
                    @foreach($columns as $col)
                        <th>{{ ucfirst(str_replace('_', ' ', $col)) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($records as $row)
                    <tr>
                        @foreach($columns as $col)
                            <td>{{ $row->$col }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    // window.onload = () => window.print();
</script>  
</body>
</html>