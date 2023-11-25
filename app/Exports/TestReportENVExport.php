<?php

namespace App\Exports;

use App\Models\AppSettings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;

// use PhpOffice\PhpSpreadsheet\Shared\Date;
// use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
// use Maatwebsite\Excel\Concerns\WithColumnFormatting;
// use Maatwebsite\Excel\Concerns\WithCustomValueBinder;


class TestReportENVExport 
                // extends \PhpOffice\PhpSpreadsheet\Cell\StringValueBinder
                implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
                // ,WithCustomValueBinder
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    public function collection()
    {
        DB::statement(DB::raw('set @rownum=0'));
        $table_data =   AppSettings::select(
                        \DB::raw("(@rownum:=@rownum + 1) AS sno"),
                        'key','values',
                        // \DB::raw("DATE(daily_report.report_date) AS daily_report"),
                        'created_at'
                        )->get();
        return $table_data;
    }


    public function headings(): array
    {
        return [
            '#',
            'key',
            'value',
            'Date',
        ];
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:D1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true)->setSize(12);
            },
        ];
    }
}
