<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function export($table, $type)
    {
        // Ensure the table exists
        if (!Schema::hasTable($table)) {
            return redirect()->back()->with('error', "Table '{$table}' does not exist.");
        }

        // Fetch all rows and columns
        $columns = Schema::getColumnListing($table);
        $records = DB::table($table)->get();

        if ($records->isEmpty()) {
            return redirect()->back()->with('error', "No data found in '{$table}' table.");
        }

        switch ($type) {
            case 'csv':
                return $this->exportCsvOrExcel($records, $columns, $table, 'csv');

            case 'excel':
                return $this->exportCsvOrExcel($records, $columns, $table, 'xlsx');

            case 'pdf':
                $pdf = Pdf::loadView('exports.dynamic_pdf', compact('records', 'columns', 'table'));
                return $pdf->download("{$table}.pdf");

            case 'print':
                return view('exports.dynamic_print', compact('records', 'columns', 'table'));

            default:
                return redirect()->back()->with('error', 'Invalid export type');
        }
    }

    private function exportCsvOrExcel($records, $columns, $table, $ext)
    {
        $callback = function() use ($records, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns); // Header row
            foreach ($records as $record) {
                fputcsv($handle, (array) $record);
            }
            fclose($handle);
        };

        $headers = [
            'Content-Type' => $ext === 'csv' ? 'text/csv' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$table}.{$ext}\"",
        ];

        return response()->stream($callback, 200, $headers);
    }
}