<?php

use Illuminate\Database\Seeder;
class AdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [ 'admin_id'=>1,
              'role_id'=>1,],

              [ 'admin_id'=>1,
              'role_id'=>2,],

              [ 'admin_id'=>1,
              'role_id'=>3,],

              [ 'admin_id'=>1,
              'role_id'=>4,],

              [ 'admin_id'=>1,
              'role_id'=>5,],

              [ 'admin_id'=>1,
              'role_id'=>6,],

              [ 'admin_id'=>1,
              'role_id'=>7,],

              [ 'admin_id'=>1,
              'role_id'=>8,],

              [ 'admin_id'=>1,
              'role_id'=>9,],

              [ 'admin_id'=>1,
              'role_id'=>10,],


            
        ];
        DB::table('admin_roles')->truncate();
        DB::table('admin_roles')->insert($data);
    }
}
