<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       	$faker = Faker\Factory::create();

    	DB::table('user')->truncate();
    	 for ($i = 1; $i <= 10; $i++) 
        {

            DB::table('user')->insert([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => 'user'.$i.'@example.com',
                'phone' => $faker->phoneNumber,
                'dob'=>$faker->date,
                'image'=>"",
                'country_id'=>rand(1,2),
                'currency_id'=>rand(1,2),
                'gender_id'=>rand(1,2),
                'remember_token'=>'',
                'suspend'=>0,
                'active'=>1,
                'password'=>bcrypt('secret'),
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            	]);
        }
    }
}
