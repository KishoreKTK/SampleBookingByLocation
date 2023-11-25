<?php

use Illuminate\Database\Seeder;

class ActionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data=[[
	        'action_type' => 'Registered',
			],
			[
	        	'action_type' => 'Activated',
			],
			[
	        	'action_type' => 'Login',
			],
			[
	        	'action_type' => 'Logout',
			],
			[
	        	'action_type' => 'Add',
			],
			[
	        	'action_type' => 'Update',
			],
			[
	        	'action_type' => 'delete',
			],
			[
	        	'action_type' => 'View',
			],
			[
	        	'action_type' => 'Booked',
			],
			[
	        	'action_type' => 'Review Added to',
			],
            [
	        	'action_type' => 'Paid ',
			],
            [
	        	'action_type' => 'Booking Approved by ',
			],
        ];
        DB::table('action_type')->truncate();
        DB::table('action_type')->insert($data);
    }
}
