<?php

use Illuminate\Database\Seeder;

class ActivityTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data=[[
	        'activity_type' => 'Account',
			],
			[
	        	'activity_type' => 'Address',
			],
			[
	        	'activity_type' => 'Salon',
			],
			[
	        	'activity_type' => 'Booking',
			],
			[
	        	'activity_type' => 'Amount',
			],
			
        ];
           
        DB::table('activity_type')->truncate();
        DB::table('activity_type')->insert($data);
    }
}
