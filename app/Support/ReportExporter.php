<?php

namespace App\Support;

use App\Exports\ArrayExport;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Excel as ExcelWriter;
use Maatwebsite\Excel\Facades\Excel;

class ReportExporter
{
    /**
     * @param  string  $format  pdf | csv | excel
     * @param  array  $summary  [label => value] printed above the table in PDFs
     */
    public static function download(string $format, string $title, array $headings, array $rows, array $summary = [], array $meta = [])
    {
        $base = Str::slug($title).'-'.now()->format('Ymd-His');

        if ($format === 'pdf') {
            return Pdf::loadView('reports.table_pdf', [
                'title' => $title,
                'headings' => $headings,
                'rows' => $rows,
                'summary' => $summary,
                'meta' => $meta,
                'shop' => [
                    'name' => Setting::get('general_application_name'),
                    'address' => Setting::get('general_address'),
                    'phone' => Setting::get('general_phone'),
                ],
            ])->setPaper('a4', count($headings) > 6 ? 'landscape' : 'portrait')->download($base.'.pdf');
        }

        if ($format === 'csv') {
            return Excel::download(new ArrayExport($headings, $rows, $title), $base.'.csv', ExcelWriter::CSV);
        }

        return Excel::download(new ArrayExport($headings, $rows, $title), $base.'.xlsx');
    }
}
