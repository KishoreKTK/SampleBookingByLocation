<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;


class SalonReport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        DB::statement(DB::raw('set @rownum=0'));
        $salon_list =   DB::table("salons")
                        ->leftJoin("countries", "countries.id","=","salons.country_id")
                        ->leftjoin(DB::raw('(SELECT
                                      B.salon_id,
                                      COUNT(B.salon_id) AS BookingCount
                                    FROM
                                      booking AS B
                                    WHERE B.deleted_at IS NOT NULL
                                    GROUP BY B.salon_id
                                ) AS Bookingtbl'),
                                function($join)
                                {
                                    $join->on('salons.id', '=', 'Bookingtbl.salon_id');
                                })
                        // ->leftjoin("salon_categories", "salon_categories.salon_id","=","salons.id")
                        ->leftjoin(DB::raw('(SELECT
                                      sc.`salon_id`,
                                      GROUP_CONCAT(sc.`category_id`) AS category_id,
                                      GROUP_CONCAT(c.`category`) AS cat_name
                                    FROM
                                      salon_categories AS sc
                                      JOIN categories AS c
                                        ON c.`id` = sc.`category_id`
                                    GROUP BY sc.`salon_id`
                            ) AS Tagtbl'),
                            function($join)
                            {
                                $join->on('salons.id', '=', 'Tagtbl.salon_id');
                            })
                    // ->leftjoin("salon_reviews", "salon_reviews.salon_id","=","salons.id")
                    ->whereNull('salons.deleted_at')
                    // ->whereNull("salon_reviews.deleted_at")
                    ->select(DB::raw("(@rownum:=@rownum + 1) AS sno"),
                            "salons.id","salons.name","salons.description","salons.email",
                            "salons.phone","salons.location","salons.city","countries.name as country",
                             DB::raw("
                            (
                                CASE
                                    WHEN salons.featured=1 THEN 'YES'
                                    ELSE 'NO'
                                END
                            ) AS featured"),
                            'salons.pricing as mood_commission','Tagtbl.cat_name',
                            // DB::raw("IFNULL( ROUND (AVG(salon_reviews.rating), 2), '0') as avgrating"),
                            DB::raw("IFNULL(Bookingtbl.BookingCount, '0') as BookingCount"),
                            "salons.image",
                            DB::raw("
                            (
                                CASE
                                    WHEN salons.active=1 THEN 'Active'
                                    WHEN salons.active=0 THEN 'InActive'
                                    ELSE '-'
                                END
                            ) AS active"),
                            DB::raw("
                            (
                                CASE
                                    WHEN salons.suspend=1 THEN 'YES'
                                    WHEN salons.suspend=0 THEN 'NO'
                                    ELSE '-'
                                END
                            ) AS suspended"),'salons.created_at')
                        ->get();
        // dd($salon_list);
        return $salon_list;
    }

    public function headings(): array
    {
        return [
            '#',
            'ID',
            'Name',
            'Description',
            'Email',
            'Contact Number',
            'Location',
            'City',
            'Counttry',
            'Featured',
            'Mood Commission',
            'Categories',
            'No.of Booking',
            'Salon image',
            'Status',
            'Suspended',
            'Created Date'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:Q1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)
                            ->getFont()->setBold(true)->setSize(12);
                // $event->sheet->getDelegate()->getStyle($cellRange)
                //             ->setWidth(array(
                //                 'D'     =>  10,
                //                 'L'     =>  10
                //             ));
                //                 ->getFont()->getColor()
                // $event->sheet->setWidth('D',15);
                // // $event->sheet
            },
        ];
    }

}
