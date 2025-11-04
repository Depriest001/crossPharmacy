<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ ucfirst($table) }} Export</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 5px; text-align: left; }
        th { background: #f8f9fa; font-weight: bold; }
    </style>
</head>
<body>
    <h3 style="text-transform: capitalize;">{{ $table }} Table</h3>
    <table>
        <thead>
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
</body>
</html>
