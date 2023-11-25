<?php

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
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
	        	'role' => 'Salons',
			],
			[
	        	'role' => 'Users',
			],
			[
	        	'role' => 'Categories',
			],
			[
	        	'role' => 'Booking',
			],
			[
	        	'role' => 'Contact Us',
			],
			[
	        	'role' => 'FAQ',
			],
			[
	        	'role' => 'Reviews',
			],
			[
	        	'role' => 'AdminUsers',
			],
			[
	        	'role' => 'Approvals',
			],
			
			


		];

    	DB::table('gen_admin_roles')->truncate();
        DB::table('gen_admin_roles')->insert($data);
    }
}
