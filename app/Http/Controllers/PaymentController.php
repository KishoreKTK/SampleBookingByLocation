<?php

namespace App\Http\Controllers;

use App\UserToken;
use Exception;
use Illuminate\Http\Request;
use App\Http\Traits\PaymentTrait;
use Illuminate\Support\Facades\DB;
class PaymentController extends Controller
{
    use PaymentTrait;

    public function AddCard()
    {
        try
        {
            $input      =   request()->all();
            $stripe = new \Stripe\StripeClient([
                "api_key"=>env('STRIPE_SECRET'),
                "stripe_version"=> "2020-08-27"
            ]);

            $card_paymentmethod =   $stripe->paymentMethods->create([
                'type' => 'card',
                'card' => [
                    'number' => request()->acc_number,
                    'exp_month' => request()->exp_month,
                    'exp_year' => request()->exp_year,
                    'cvc' => request()->cvc,
                ],
            ]);

            $payment_method_id  =   $card_paymentmethod->id;

            if(request()->has('save_for_future_use') && request()->save_for_future_use == true){

            // }
            // if($input['save_for_future_use'] == true)
            // {
                $api_token  =   request()->header('User-Token');
                $user_exists    =   UserToken::where("api_token",$api_token)->first();
                if(!$user_exists)
                {
                    throw new Exception("Please Login to Continue");
                }

                $user_id = $user_exists->user_id;

                $generated_finger_print = $card_paymentmethod['card']->fingerprint;
                $check_token_exists     =   DB::table('save_cards')->where('user_id',$user_id)->where('fingerprint',$generated_finger_print)->first();
                if(!$check_token_exists)
                {
                    $user_exists     =   DB::table('save_cards')->where('user_id',$user_id)->first();
                    if(!$user_exists)
                    {
                        $customer       =   $stripe->customers->create([
                            'description' => 'CustomerID created for UserId'.$user_id.' Since Customer wants to Save the Card',
                        ]);
                        $customer_id    =   $customer->id;
                    } else {
                        $customer_id    =   $user_exists->customer_id;
                    }

                    $card_detail        = $card_paymentmethod['card'];
                    $card_fingerprint   =   $card_detail->fingerprint;
                    $exp_year           =   $card_detail->exp_year;
                    $exp_month          =   $card_detail->exp_month;
                    $lastfourdigit      =   $card_detail->last4;

                    $attach_payment_method_to_customer =    $stripe->paymentMethods->attach(
                                                                $payment_method_id,
                                                                ['customer' => $customer_id]
                                                            );

                    if(!$attach_payment_method_to_customer){
                        throw new Exception("Some Problem with Attaching payment with customers");
                    }
                    $insert_data                  =     [];
                    $insert_data['acc_holder_name']=    $input['acc_holder_name'];
                    $insert_data['user_id']       =     $user_id;
                    $insert_data['customer_id']   =     $customer_id;
                    $insert_data['card_token']    =     $payment_method_id;
                    $insert_data['fingerprint']   =     $card_fingerprint;
                    $insert_data['acc_number']    =     $this->ccMasking($input['acc_number']);
                    $insert_data['lastfourdigit'] =     $lastfourdigit;
                    $insert_data['exp_month']     =     $exp_month;
                    $insert_data['exp_year']      =     $exp_year;
                    $insert_data['created_at']    =     date('Y-m-d H:i:s');
                    $insert_card_id               =     DB::table('save_cards')->insertGetId($insert_data);
                    if($insert_card_id)
                    {
                        $result     =   [
                                            'status'=>true,
                                            'payment_metod_id'=>$payment_method_id,
                                            'message'=>'Card Added Succesfully'
                                        ];
                    }
                    else
                    {
                        throw new Exception("Something Went Wrong. Try Later");
                    }
                } else {
                    // $result     =   [
                    //     'status'=>false,
                    //     'payment_metod_id'=>$check_token_exists->card_token,
                    //     'message'=>'Card Already Exists'
                    // ];
                    throw new Exception("Card Already Exists. Please Select Card to Continue");
                }
            } else {
                $result     =   [
                    'status'=>true,
                    'payment_metod_id'=>$payment_method_id,
                    'message'=>'Card Id Created'
                ];
            }
        } catch(Exception $e){
            $result         =   ['status'=>false, 'message'=>$e->getMessage()];
        }
        return response()->json($result);
    }

    public function GetCardDetails(){
        try
        {
            $api_token      =   request()->header('User-Token');
            $user_exists    =   UserToken::where("api_token",$api_token)->first();
            if(!$user_exists)
            {
                throw new Exception("Please Login to Continue");
            }
            $card_id    =   request()->card_id;
            $check_card_id     =   DB::table('save_cards')->where('id',$card_id)->first();
            if($check_card_id){
                $result     =   [
                    'status'=>true,
                    'payment_metod_id'=>$check_card_id,
                    'message'=>'Card Detials listed'
                ];
            } else {
                throw new Exception("Please Check Card Id");
            }

        } catch(Exception $e){
            $result         =   ['status'=>false, 'message'=>$e->getMessage()];
        }
        return response()->json($result);
    }

    public function listCards(){
        try{
            $api_token  =   request()->header('User-Token');
            if(!$api_token){
                throw new Exception("Please Login to Continue");
            }
            $user_id    =   UserToken::where("api_token",$api_token)->first()->user_id;
            $result     =   [
                                'status'=>true,
                                'data'=>DB::table('save_cards')
                                        ->where('user_id',$user_id)->get(),
                                'message'=>'Cards listed Succesfully'
                            ];
        } catch(Exception $e) {
            $result         =   ['status'=>false, 'message'=>$e->getMessage()];
        }return response()->json($result);
    }

    public function DeleteCard(){
        try{
            $api_token  =   request()->header('User-Token');
            $user_id    =   UserToken::where("api_token",$api_token)->first()->user_id;
            $card_id    =   request()->card_id;
            $delete_card=   DB::table('save_cards')->where('id',$card_id)->delete();
            if($delete_card){
                $result     =   [
                    'status'=>true,
                    'message'=>'Cards Removed Succesfully'
                ];
            } else {
                throw new Exception("Please Check Card Number");
            }
        } catch(Exception $e) {
            $result         =   ['status'=>false, 'message'=>$e->getMessage()];
        }return response()->json($result);
    }

    public function SetDefaultCard(){
        try{
            $api_token  =   request()->header('User-Token');
            $user_id    =   UserToken::where("api_token",$api_token)->first()->user_id;
            $card_id    =   request()->card_id;
            $update_existing_cards = DB::table('save_cards')->where('user_id',$user_id)->update(['default_card'=>'0','updated_at'=>date('d-m-Y H:i:s')]);
            if(!$update_existing_cards){
                throw new Exception("Error in Updating Card.");
            }
            $set_default_card = DB::table('save_cards')->where('id',$card_id)->update(['default_card'=>'1','updated_at'=>date('d-m-Y H:i:s')]);
            if(!$set_default_card){
                throw new Exception("Error in Updating Card.");
            }
            $result     =   [
                                'status'=>true,
                                'message'=>'Cards Removed Succesfully'
                            ];
        } catch(Exception $e) {
            $result         =   ['status'=>false, 'message'=>$e->getMessage()];
        }
        return response()->json($result);
    }

}
