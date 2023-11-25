<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notifiable;


class NotifyUsers extends Command
{
    use Notifiable;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        // $booking = DB::table('booking')->get();
        // foreach($booking as $user) {
        //   $date = $user->bookdate;

        //   $user->notify("Your Booking Date....!");
        // }
        // return Command::SUCCESS;
        $firebaseToken  =   [
            'cO-VC6Qs7aGl3TZYtNk3v6:APA91bGVw6SoTcakZZR92LGZVn04C4u3d0PvcQcEi7O6WaLhruqbznGXlmBxPKaFA9evWFeS8FbO19JVk0x06go8iTupJ_U2R-LDTU3uRQH7muQ9ew5Jn5zOnRh-XId2DOOaiuzo3KOs',
            'd5OQBGBiMEED5HKmaud3Wi:APA91bGQcTfxyrDr-rWHZiyjNWqCR-ASkX_aFgknappUleJV9qVY0nlxDFN08WcT6SOwZ1XCyPJ81XvnxsHvUhKBQo0HDAaX1ppR7hSUBVd9DP38yh-Z8qELuMu8XOChXQauP_hhZ5RY',
            'cNmVe586TX6q1xmdj9dSOm:APA91bH1_jNSp1qOPp6zeI0q7uoryPfSeEn6wsYlBbeXL7g0T-0RMp62RCXGXk38AC8CUnHVa6pJ2q2UuOohktkGK_xvBFr7SVhL6YblGJf8tI1Q3npaLreX5bDw2Wh_bhLfv3wwq77P'
        ];
        $SERVER_API_KEY = env('NOTIFICATION_SERVER_KEY');

        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => "Booking Remainder",
                "body" => "You have Booking Today Starts at 10am",
            ]
        ];
        $dataString = json_encode($data);

        $headers = [
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
