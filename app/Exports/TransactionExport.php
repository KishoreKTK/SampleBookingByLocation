<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Events\AfterSheet;


class TransactionExport implements FromArray,WithHeadings,ShouldAutoSize,WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $transaction;

    public function __construct(array $transaction)
    {
        $this->transaction = $transaction;
    }

    public function array(): array
    {
        return $this->transaction;
    }

    public function headings(): array
    {
        return [
            '#',
            'Salon',
            'Booked By',
            'Amount Paid',
            'Mood Commission',
            'VAT Amount',
            'Actual amount'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:G1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)
                            ->getFont()->setBold(true)->setSize(12);
            },
        ];
    }

}
