<?php
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ContactUsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

    	DB::table('contact_us')->truncate();
    	 for ($i = 1; $i <= 20; $i++) 
        {

            DB::table('contact_us')->insert([
                'name' => $faker->firstName,
                'email' => 'user'.$i.'@example.com',
                'subject'=>"Lorem Ipsum.",
                'description'=>"Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
               'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            	]);
        }
    }
}
