<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use DB;
use Config;
class EnvCustomServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $mail   = DB::table('app_settings')->where('key', 'like', '%MAIL_%')->get();
        $google = DB::table('app_settings')->where('key', 'like', '%GOOGLE_%')->get();
        if(count($mail) > 0) //checking if table is not empty
        {
            $config = [];
            foreach($mail as $env)
            {
                if($env->key == 'MAIL_MAILER') {
                    $config['driver'] = $env->values;
                }
                elseif($env->key == 'MAIL_HOST') {
                    $config['host'] = $env->values;
                }
                elseif($env->key == 'MAIL_PORT') {
                    $config['port'] = $env->values;
                }
                elseif($env->key == 'MAIL_FROM_ADDRESS') {
                    $config['from']['address'] = $env->values;
                }
                elseif($env->key == 'MAIL_FROM_NAME') {
                    $config['from']['name'] = $env->values;
                }
                elseif($env->key == 'MAIL_ENCRYPTION') {
                    $config['encryption'] = $env->values;
                }
                elseif($env->key == 'MAIL_USERNAME') {
                    $config['username'] = $env->values;
                }
                elseif($env->key == 'MAIL_PASSWORD') {
                    $config['password'] = $env->values;
                }
            }
        }
        else
        {
              $config = array(
                    'driver'     => "smtp",
                    'host'       => "smtp.gmail.com",
                    'port'       => "587",
                    'from'       => array('address' => '', 'name' => 'project_name'),
                    'encryption' => "tls",
                    'username'   => '',
                    'password'   => '',             
                );
        }
        Config::set('mail', $config);

        if(count($google)>0){
            $google_config = [];
            foreach($google as $env)
            {
                if($env->key == 'GOOGLE_CLIENT_ID') {
                    $google_config['GOOGLE_CLIENT_ID'] = $env->values;
                }
                elseif($env->key == 'GOOGLE_CLIENT_SECRET') {
                    $google_config['GOOGLE_CLIENT_SECRET'] = $env->values;
                }
                elseif($env->key == 'GOOGLE_CALLBACK_URL') {
                    $google_config['GOOGLE_CALLBACK_URL'] = $env->values;
                }
            }
        }
        else{
            $google_config['GOOGLE_CLIENT_ID'] = null;
            $google_config['GOOGLE_CLIENT_SECRET'] = null;
            $google_config['GOOGLE_CALLBACK_URL'] = null;
        }
        

        Config::set('google_credentials', $google_config);

    }
    
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
