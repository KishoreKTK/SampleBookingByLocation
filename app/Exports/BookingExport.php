<?php

namespace App\Exports;

// use Maatwebsite\Excel\Concerns\FromCollection;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;

class BookingExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $booking_data;

    public function __construct($data) {
        $this->booking_data = $data;
    }

    public function collection()
    {
        return $this->booking_data;
    }

    public function headings(): array
    {
        return [
            '#',
            'ID',
            'Salon',
            'Date',
            'Start Time',
            'End Time',
            'Staffs',
            'Promocode',
            'Amount',
            'Special Requests',
            'Booking Status',
            'Customer Name',
            'Cuustomer Email',
            'Booked Services',
            'Booked Date'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:O1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)
                            ->getFont()->setBold(true)->setSize(12);
            },
        ];
    }
}
