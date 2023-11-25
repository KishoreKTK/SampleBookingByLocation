<?php

use Illuminate\Database\Seeder;

class SalonRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data=[[
	        'role' => 'Dashboard',
			],
			[
	        	'role' => 'Booking',
			],
			[
	        	'role' => 'Services',
			],
			[
	        	'role' => 'Staff',
			],
			[
	        	'role' => 'Working Hours',
			],
			[
	        	'role' => 'Reviews',
			],
			[
	        	'role' => 'Categories',
			],
			[
	        	'role' => 'Salon Users',
			],
			[
	        	'role' => 'Schedules',
            ],
            [
	        	'role' => 'Offers',
			]
			


		];

    	DB::table('sroles')->truncate();
        DB::table('sroles')->insert($data);
    }
}
