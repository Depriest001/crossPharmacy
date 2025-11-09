<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exports\ArrayExport; // ✅ new reusable export class

class ExportController extends Controller
{
    public function export($table, $type)
    {
        if (!Schema::hasTable($table)) {
            return redirect()->back()->with('error', "Table '{$table}' does not exist.");
        }

        // Get all table columns
        $columns = Schema::getColumnListing($table);

        // Exclude sensitive/unwanted columns but KEEP created_at
        $excluded = ['password', 'remember_token', 'updated_at', 'deleted_at'];
        $columns = array_values(array_diff($columns, $excluded));

        // Fetch the records
        $records = DB::table($table)->select($columns)->get();

        // Replace foreign keys with readable names where possible
        $records = $this->replaceForeignKeys($table, $records);

        if ($records->isEmpty()) {
            return redirect()->back()->with('error', "No data found in '{$table}' table.");
        }

        switch ($type) {
            case 'csv':
                return $this->exportCsv($records, $columns, $table);

            case 'excel':
                // ✅ Convert collection to array for Excel export
                $exportData = $records->map(function ($record) {
                    return (array) $record;
                })->toArray();

                return Excel::download(new ArrayExport($exportData), "{$table}.xlsx");

            case 'pdf':
                $pdf = Pdf::loadView('exports.dynamic_pdf', compact('records', 'columns', 'table'))
                    ->setPaper('A4', 'landscape'); // optional for better table layout
                return $pdf->download("{$table}.pdf");

            default:
                return redirect()->back()->with('error', 'Invalid export type.');
        }
    }

    private function exportCsv($records, $columns, $table)
    {
        $callback = function() use ($records, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);
            foreach ($records as $record) {
                fputcsv($handle, (array) $record);
            }
            fclose($handle);
        };

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$table}.csv\"",
        ];

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Replace known foreign key IDs with readable names.
     */
    private function replaceForeignKeys($table, $records)
    {
        foreach ($records as $record) {
            // Format created_at
            if (isset($record->created_at)) {
                $record->created_at = Carbon::parse($record->created_at)->format('M d, Y h:i:s A');
            }
        }

        // Replace staff role_id and branch_id with names
        if ($table === 'staff') {
            foreach ($records as $record) {
                if (isset($record->role_id)) {
                    $record->role_id = DB::table('roles')->where('id', $record->role_id)->value('name') ?? '-';
                }
                if (isset($record->branch_id)) {
                    $record->branch_id = DB::table('branches')->where('id', $record->branch_id)->value('name') ?? '-';
                }
            }
        }

        // Replace product category_id with category name
        if ($table === 'products') {
            foreach ($records as $record) {
                if (isset($record->category_id)) {
                    $record->category_id = DB::table('categories')->where('id', $record->category_id)->value('name') ?? '-';
                }
            }
        }

        return $records;
    }
}