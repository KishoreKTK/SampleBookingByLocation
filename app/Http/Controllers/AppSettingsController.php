<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppSettings;
use App\Exports\TestReportENVExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Excel;

class AppSettingsController extends Controller
{

    public function TestMail(){
        try{
            $test = Mail::raw('Hello World!', function($msg) {
                $msg->to('ktkkishore@gmail.com')->subject('Test Email');
            });
            echo "Mail Sent sucessfully";
        }
        catch(\Exception $e){
            echo "error : ". $e;
        }
    }



    public function TestMailCredentials()
    {
        $mail = DB::table('app_settings')->where('key', 'like', '%MAIL_%')->get();
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
        else{
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

        // print("<pre>");
        // print_r( $config);die;

        // MAIL_MAILER=smtp
        // MAIL_HOST=smtp.mailtrap.io
        // MAIL_PORT=2525
        // MAIL_USERNAME=154d412909e18e
        // MAIL_PASSWORD=f2374653fb0315
        // MAIL_ENCRYPTION=tls

        // Array
        // (
        //     [driver] => smtp
        //     [host] => smtp.mailtrap.io
        //     [port] => 2525
        //     [username] => 154d412909e18e
        //     [password] => f2374653fb0315
        //     [encryption] => tls
        //     [from] => Array
        //         (
        //             [address] => kishore.kuwy@gmail.com
        //             [name] => Kishore KTK
        //         )
        // )

       return $config;
    }

    public function CheckDBConnection()
    {
        if(DB::connection()->getDatabaseName())
        {
            $data =  "conncted sucessfully to database ".DB::connection()->getDatabaseName();
        }
        else{
            $data = "error in connection please check";
        }
        return $data;
    }

    public function envindex()
    {
        $ENV_list =   AppSettings::all();
        return view('AppSettings',Compact('ENV_list'));
    }

    public function envstore()
    {
        AppSettings::create(request()->all());
        return back()->with('message','New AppSettings Added');
    }


    public function export()
    {
        // DB::statement(DB::raw('set @rownum=0'));
        //     $table_data =   AppSettings::select(
        //                 \DB::raw("(@rownum:=@rownum + 1) AS sno"),
        //                 'key','values',
        //                 // 'created_at'
        //                 \DB::raw(" DATE_FORMAT('DATE(created_at', '%Y-%m-%d %T') as created_at")
        //                 )->get();
        //     dd($table_data);die;
        return Excel::download(new TestReportENVExport, 'ENV.xlsx');
    }

    
    // public function envedit()
    // {
    //     $input  = request()->validate([
    //         'AppSettings_name'  => 'required|min:3|unique:categories',
    //     ]);
    //     $input['AppSettings_name']    = Str::ucfirst(Str::lower($input['AppSettings_name']));
    //     $input['Status']    = '0';
    //     AppSettings::create($input);
    //     return back()->with('message','New AppSettings Added');
    // }

    // public function envdelete()
    // {
    //     $input  = request()->validate([
    //         'AppSettings_name'  => 'required|min:3|unique:categories',
    //     ]);
    //     $input['AppSettings_name']    = Str::ucfirst(Str::lower($input['AppSettings_name']));
    //     $input['Status']    = '0';
    //     AppSettings::create($input);
    //     return back()->with('message','New AppSettings Added');
    // }
}
