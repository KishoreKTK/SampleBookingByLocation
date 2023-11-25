<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;


class UserReportExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //
        DB::statement(DB::raw('set @rownum=0'));
         $excel_data    =   DB::table("user")
                            ->leftJoin("countries", "countries.id","=","user.country_id")
                            ->whereNull('user.deleted_at')
                            ->select(DB::raw("(@rownum:=@rownum + 1) AS sno"),
                                    "user.id","user.first_name",
                                    DB::raw("IFNULL(user.last_name, '-') as lastname"),
                                    "user.email",
                                    DB::raw("IFNULL(user.phone, '-') as PhoneNum"),
                                    DB::raw("
                                        (
                                            CASE
                                                WHEN user.gender_id=1 THEN 'Male'
                                                WHEN user.gender_id=2 THEN 'Female'
                                                ELSE '-'
                                            END
                                        ) AS gender"),
                                    DB::raw("IFNULL(DATE(user.dob), '-') AS dob"),
                                    DB::raw("IFNULL(user.image, '-') AS image"),
                                    "countries.name as country",
                                    DB::raw("
                                        (
                                            CASE
                                                WHEN user.suspend=0 THEN 'Active'
                                                WHEN user.suspend=1 THEN 'suspended'
                                                ELSE '-'
                                            END
                                        ) AS user_status"),
                                    DB::raw("IFNULL(user.login_type, '-') AS login_type"),
                                    "user.created_at")
                            ->get();
        return $excel_data;
    }

    public function headings(): array
    {
        return [
            '#',
            'USER ID',
            'FIRST NAME',
            'LAST NAME',
            'EMAIL',
            'PHONE NUMBER',
            'GENDER',
            'DOB',
            'PROFILE PICTURE',
            'COUNTRY',
            'USER STATUS',
            'LOGIN TYPE',
            'CREATED DATE'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:M1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true)->setSize(12);
            },
        ];
    }

}
