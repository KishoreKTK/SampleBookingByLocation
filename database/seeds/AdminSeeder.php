<?php

use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $data=[
            'name' => "Admin",
            'email' => 'admin@mood.com',
            'password' => bcrypt('mood_admin'),
            'master_admin' => 1,
            'active' => 1,
            'suspend' => 0,
            'remember_token'=>'',
            'api_token'=>'',

        		];
    	DB::table('admin')->truncate();
        DB::table('admin')->insert($data);
    }
}
