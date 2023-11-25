<?php

namespace App\Http\Controllers;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Stripe;
use Stripe\StripeClient;
use Illuminate\Http\Request;

class TestController extends Controller
{
    //
    public function CheckStripeAPI(){
        // $response = Http::get('https://api.stripe.com');
        // $stripe = new \Stripe\StripeClient("sk_test_51JoNj6HBN2bK41VW9Xf0iKYagtQWaTSFD1U6npPqd449WN9rMoO3tGYjxx0ZmkUIv1vqKuEA69GBuKEk2yXnloV200G5COsdXn");
        return Http::dd()->get('https://api.stripe.com');
    }

    public function stripePost(Request $request)
    {


        $stripe = new \Stripe\StripeClient([
            "api_key"=>env('STRIPE_SECRET'),
            "stripe_version"=> "2020-08-27"
        ]);


        // Payment Intent

        //  Charges


        // // Create Customer
        // $customer_id =  $stripe->customers->create([
        //                     'description' => 'Customer ID created for this Version Id',
        //                 ]);
        // dd($customer_id);
        // cus_KujhQVtcyG9ZWQ


        $token = $stripe->tokens->create([
            'card' => [
            'number' => '4242424242424242',
            'exp_month' => 03,
            'exp_year' => 2025,
            'cvc' => '314',
            // 'number' => $request->get('card_no'),
            // 'exp_month' => $request->get('ccExpiryMonth'),
            // 'exp_year' => $request->get('ccExpiryYear'),
            // 'cvc' => $request->get('cvvNumber'),
            ],
        ]);
        dd($token);
        // tok_1KEuEnHBN2bK41VW3BCX8SWE

        // Do Payment Using Charges
        // $charge = $stripe->charges->create([
        //         "amount" => 900 * 100,
        //         "currency" => "aed",
        //         "source" => "tok_1K3vr1HBN2bK41VWEoMRgJDp",
        //         "description" => "This payment is tested purpose"
        // ]);
        // dd($charge);


        // // Create Payment Method;
        // $card_paymentmethod =   $stripe->paymentMethods->create([
        //                             'type' => 'card',
        //                             'card' => [
        //                             'number' => '4242424242424242',
        //                             'exp_month' => 12,
        //                             'exp_year' => 2022,
        //                             'cvc' => '314',
        //                             ],
        //                         ]);
        // dd($card_paymentmethod);
        // pm_1KEuFrHBN2bK41VWdVoPoLvy


        // Retrieve Payment
        // $get_payment_method =   $stripe->paymentMethods->retrieve(
        //                             'pm_1K6sCIHBN2bK41VWx8OzLMBq',
        //                             []
        //                         );
        // dd($get_payment_method);

        // Attach Payment Method to the Customer
        // $attach_payment_method_to_customer = $stripe->paymentMethods->attach(
        //                                         'pm_1KEuFrHBN2bK41VWdVoPoLvy',
        //                                         ['customer' => 'cus_KujhQVtcyG9ZWQ']
        //                                     );
        // dd($attach_payment_method_to_customer);
        // pm_1KEuFrHBN2bK41VWdVoPoLvy

        // // Create Payment Intent
        $create_payment_intent  =   $stripe->paymentIntents->create([
                                        "amount" => 800 * 100,
                                        "currency" => "aed",
                                        "customer"  => "cus_KujhQVtcyG9ZWQ",
                                        "payment_method_types" => ['card'],
                                    ]);
        // dd($create_payment_intent->id);

        // pi_3KEuJuHBN2bK41VW0V2cmDzu
        // pi_3KEuLuHBN2bK41VW0sHv4oe5

        // // Confirm Payment Intent
        $confirm_payment_intent =   $stripe->paymentIntents->confirm(
                                        $create_payment_intent->id,
                                        ['payment_method' => 'pm_1KEuFrHBN2bK41VWdVoPoLvy']
                                    );

        print("<pre>");print_r($confirm_payment_intent->charges['data'][0]->id);die;

        // pi_3KEuJuHBN2bK41VW0V2cmDzu

        // //  Create SetupIntent
        // $create_setup_intent    =   $stripe->setupIntents->create([
        //                                 "customer"  => "cus_KmSE5OwviqTD7X",
        //                                 'payment_method_types' => ['card'],
        //                             ]);
        // dd($create_setup_intent);


        // $setupIntents_confirm   =   $stripe->setupIntents->confirm(
        //                                 'seti_1K6tsMHBN2bK41VWVqb9abeU',
        //                                 ['payment_method' => 'pm_1K6tKsHBN2bK41VW1RBBxmaS']
        //                             );
        // dd($setupIntents_confirm);





        // $confirm_payment_intent  =   $stripe->paymentIntents->confirm(
        //                              'pi_3K6tLuHBN2bK41VW0Dvg0RFJ',
        //                              ['payment_method' => 'pm_1K6tKsHBN2bK41VW1RBBxmaS']
        //                             );

        // $data =     $stripe->paymentIntents->retrieve(
        // 'pi_3K2YXfHBN2bK41VW0R1pMZGR',[]
        // );

        // $charge = $stripe->charges->create([
        //         'card' => $token['id'],
        //         'currency' => 'USD',
        //         'amount' => 20.49,
        //         'description' => 'wallet',
        //         ]);


        // dd($create_payment_intent);
        // $stripe = Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        // $stripe->paymentIntents->retrieve('pi_3K2B5FHBN2bK41VW129OmxZ3');

        // print("<pre>");
        // return $data;
        // print_r($create_payment_intent);
        // die;

        //    Create Token
        // $stripe = new \Stripe\StripeClient(
        //     'sk_test_51JoNj6HBN2bK41VW9Xf0iKYagtQWaTSFD1U6npPqd449WN9rMoO3tGYjxx0ZmkUIv1vqKuEA69GBuKEk2yXnloV200G5COsdXn'
        //     );

        // $card_token =     $stripe->tokens->create([
        //         'card' => [
        //             'number' => '4242424242424242',
        //             'exp_month' => 12,
        //             'exp_year' => 2022,
        //             'cvc' => '314',
        //         ],
        // ]);

        // $token_detials =    $stripe->tokens->retrieve(
        //                         'tok_1K3eiYHBN2bK41VWiaD9lHJK',
        //                         []
        //                     );

        // print("<pre>");print_r($token_detials);die;

        // tok_1K3bGyHBN2bK41VWALxSu1Zr

        // tok_1K3bHDHBN2bK41VW7r0Uvw75

        // tok_1K3bGMHBN2bK41VWfgtRdfM2


        // $stripe = new \Stripe\StripeClient(
        //     'sk_test_51JoNj6HBN2bK41VW9Xf0iKYagtQWaTSFD1U6npPqd449WN9rMoO3tGYjxx0ZmkUIv1vqKuEA69GBuKEk2yXnloV200G5COsdXn'
        // );

        // $retrieve_token     =   $stripe->tokens->retrieve(
        //                             'tok_1K3bGMHBN2bK41VWfgtRdfM2',
        //                             []
        //                         );
        // dd($retrieve_token);
    }


    public function GenerateEmpheralCode(Request $request){
        try{
            $stripe = new \Stripe\StripeClient([
                "api_key"=>env('STRIPE_SECRET'),
                "stripe_version"=> "2020-08-27"
            ]);
            // dd($e_token);

            // Create Customer
            $customer_id =  $stripe->customers->create([
                                'description' => 'Customer ID created for this Version Id',
                            ]);

            // $empheralresponse  = Http::
            //                     // withBasicAuth(env('STRIPE_SECRET'),'')
            //                     // ->accept('application/x-www-form-urlencoded')
            //                     withHeaders([
            //                         'Authorization'=>'Basic '.env('STRIPE_SECRET'),
            //                         'Content-Type' => 'application/x-www-form-urlencoded',
            //                         'Stripe-Version' => '2020-08-27',
            //                     ])->post('https://api.stripe.com/v1/ephemeral_keys', [
            //                         'customer'      => "cus_KuIPRpan4YT9KB"
            //                     ]);
            // dd(json_decode($empheralresponse));

            // $params         =   ['customer'      => "cus_KuIPRpan4YT9KB"];
            // $data = json_encode($params);
            // // dd($data);
            // $headers        =   [
            //     'Authorization' => 'Bearer '.env('STRIPE_SECRET'),
            //     'Stripe-Version' => '2020-08-27',
            //     'Content-Type'=>'',
            // ];

            $curl           =   curl_init();
            curl_setopt_array($curl, array
            (
                CURLOPT_URL => 'https://api.stripe.com/v1/ephemeral_keys',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_POSTFIELDS => "customer=$customer_id->id",
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer " . env('STRIPE_SECRET'),
                    'Content-Type: application/x-www-form-urlencoded',
                    'Connection: Keep-Alive',
                    'Stripe-Version: 2020-08-27'
                ],
            ));

            $res = curl_exec($curl);
            $empheral =json_decode($res);
            // dd($res);
            $err = curl_error($curl);
            $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            // $response = Http::withBasicAuth('taylor@laravel.com', 'secret')->post()
            $result =   ['status'=>true, 'customer_id'=>$customer_id->id,'empheral_key'=>$empheral->id];
        } catch(Exception $e){
            $result =   ['status'=>false, 'message'=>$e->getMessage()];
        }
        return response()->json($result);
    }




    public function CheckSMS(Request $request){
        try{
            $mobile         =   '971543845043';
            $otp = rand(100000, 999999);
            // dd($otp);
            $response = Http::get('http://smpplive.com/api/send_sms/single_sms', [
                'to' => $mobile,
                'username' => 'Mood App',
                'password' => 'Mood2021',
                'from' => 'Mood App',
                'content' => 'Testing Purpose SMS - ' . $otp
            ]);
            // $response = Http::get('http://smpplive.com/api/send_sms/single_sms', [
            //     'to' => $mobile,
            //     'username' => 'Mood App',
            //     'password' => 'Mood2021',
            //     'from' => 'SKYOTP',
            //     'content' => 'Your app name verification code is ' . $otp
            // ]);
            dd($response);


            // =json_decode($res);
                // $request = new HttpRequest();
                // $request->setUrl('http://smpplive.com/api/send_sms/single_sms');
                // $request->setMethod(HTTP_METH_GET);
                // $request->setQueryData(array(
                // 'to' => '123456789',
                // 'username' => 'username',
                // 'password' => '12345678',
                // 'from' => 'SMS',
                // 'content' => 'hi how are you'
                // ));
                // $request->setHeaders(array(
                // 'cache-control' => 'no-cache'
                // ));
            //     try {
            //     $response = $request->send();
            //     echo $response->getBody(); }
            //     catch (HttpException $ex) { echo
            //     $ex;
            // }
            // $curl           =   curl_init();
            // curl_setopt_array($curl, array
            // (
            //     CURLOPT_URL => 'https://smpplive.com/api/send_sms/single_sms?to='.$mobile.'
            // &username=kishorektk&password=Test@123&from=SMS&content=SmS for Testing Purpose',
            //     CURLOPT_RETURNTRANSFER => true,
            //     CURLOPT_ENCODING => "",
            //     CURLOPT_MAXREDIRS => 10,
            //     CURLOPT_TIMEOUT => 30,
            //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //     CURLOPT_CUSTOMREQUEST => "GET",
            // ));

            // $res = curl_exec($curl);
            // $err = curl_error($curl);
            // $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            // curl_close($curl);

            // if($err)
            // {
            //     throw new Exception("Failed to get account details");
            // }
            // elseif($status != 200)
            // {
                // throw new Exception("Something Went Wrong. Please Check Again Later");
            //     $response=json_decode($res);
            //     $return['error']=true;
            //     $return["msg"]="Error occured";
            //     $return["response"]=$response;
            //     return $return;
            // } else{
            //     $response=json_decode($res);
            //     $result = ['status'=>true, "data"=>$response,'message'=>"Mobile Number Verification Successful"];
            // }
        } catch(Exception $e){
            $result =   ['status'=>false, 'message'=>$e->getMessage()];
        }
        return response()->json($result);
    }

    public function SendNotification(Request $request){
        // $firebaseToken = User::whereNotNull('device_token')->pluck('device_token')->all();
        $firebaseToken  =   [
                                'cO-VC6Qs7aGl3TZYtNk3v6:APA91bGVw6SoTcakZZR92LGZVn04C4u3d0PvcQcEi7O6WaLhruqbznGXlmBxPKaFA9evWFeS8FbO19JVk0x06go8iTupJ_U2R-LDTU3uRQH7muQ9ew5Jn5zOnRh-XId2DOOaiuzo3KOs',
                                'd5OQBGBiMEED5HKmaud3Wi:APA91bGQcTfxyrDr-rWHZiyjNWqCR-ASkX_aFgknappUleJV9qVY0nlxDFN08WcT6SOwZ1XCyPJ81XvnxsHvUhKBQo0HDAaX1ppR7hSUBVd9DP38yh-Z8qELuMu8XOChXQauP_hhZ5RY',
                                'cNmVe586TX6q1xmdj9dSOm:APA91bH1_jNSp1qOPp6zeI0q7uoryPfSeEn6wsYlBbeXL7g0T-0RMp62RCXGXk38AC8CUnHVa6pJ2q2UuOohktkGK_xvBFr7SVhL6YblGJf8tI1Q3npaLreX5bDw2Wh_bhLfv3wwq77P'
                            ];
        $SERVER_API_KEY = env('NOTIFICATION_SERVER_KEY');

        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => $request->title,
                "body" => $request->body,
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

        $response = curl_exec($ch);

        dd($response);
    }
}
