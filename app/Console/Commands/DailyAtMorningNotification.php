<?php

namespace App\Console\Commands;

use App\UserToken;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DailyAtMorningNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'morning:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily Morning notification for Customer at 9am';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today          =   date('d-m-Y');
        $Booking        =   DB::table('booking')->where('bookdate',$today)
                            ->where('block','0')->where('active','1')->where('user_id','!=',0)
                            ->whereNull('deleted_at')->get();

        foreach ($Booking as $key => $bk)
        {
            $bked_dt = $bk->bookdate;
            if(strtotime($bked_dt) == strtotime($today))
            {
                // $firebaseToken  =   [
                //     'cO-VC6Qs7aGl3TZYtNk3v6:APA91bGVw6SoTcakZZR92LGZVn04C4u3d0PvcQcEi7O6WaLhruqbznGXlmBxPKaFA9evWFeS8FbO19JVk0x06go8iTupJ_U2R-LDTU3uRQH7muQ9ew5Jn5zOnRh-XId2DOOaiuzo3KOs',
                //     'd5OQBGBiMEED5HKmaud3Wi:APA91bGQcTfxyrDr-rWHZiyjNWqCR-ASkX_aFgknappUleJV9qVY0nlxDFN08WcT6SOwZ1XCyPJ81XvnxsHvUhKBQo0HDAaX1ppR7hSUBVd9DP38yh-Z8qELuMu8XOChXQauP_hhZ5RY',
                //     'cNmVe586TX6q1xmdj9dSOm:APA91bH1_jNSp1qOPp6zeI0q7uoryPfSeEn6wsYlBbeXL7g0T-0RMp62RCXGXk38AC8CUnHVa6pJ2q2UuOohktkGK_xvBFr7SVhL6YblGJf8tI1Q3npaLreX5bDw2Wh_bhLfv3wwq77P'
                // ];
                $device_tokens  =   [];
                $bookstrttime   =   $bk->bookstrttime;
                $start_time     =   strtotime($bookstrttime);
                $newstartTime   =   date("H:i", strtotime('+30 minutes', $start_time));
                $user_id        =   $bk->user_id;
                $user_fcm       =   UserToken::where('user_id',$user_id)
                                    ->whereNotNull('fcm')->wherenull('deleted_at')->pluck('fcm')->toArray();
                // if($user){
                //     $user_fcm[] =   $user_fcm->user_id;
                //     array_push($device_tokens,$user->fcm);
                // }
                $notificationdata = [
                    "title" => "Booking Reminder",
                    "body" => "Today you have Booking that Starts at ".$newstartTime ."."
                ];

                $SERVER_API_KEY = env('NOTIFICATION_SERVER_KEY');
                $data   =   [
                                "registration_ids" => $user_fcm,
                                "notification" => $notificationdata
                            ];

                $dataString = json_encode($data);
                $headers =  [
                                'Authorization: key=' . $SERVER_API_KEY,
                                'Content-Type: application/json',
                            ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
                curl_exec($ch);
            }
        }
    }
}
