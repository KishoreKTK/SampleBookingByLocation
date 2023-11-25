<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
          $faker = Faker\Factory::create();

    	DB::table('faq')->truncate();
    	 for ($i = 1; $i <= 10; $i++) 
        {

            DB::table('faq')->insert([
                'title'=>"Lorem Ipsum.",
                'category_id'=>1,
                'description'=>"Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
               'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            	]);
        }
    }
}

