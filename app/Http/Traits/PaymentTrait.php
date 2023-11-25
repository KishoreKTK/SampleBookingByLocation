<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use League\OAuth1\Client\Server\Server;
use Maatwebsite\Excel\Facades\Excel;
use Stripe;
use Stripe\StripeClient;

trait PaymentTrait {

    function ccMasking($number, $maskingCharacter = 'X') {
        return substr($number, 0, 4) . str_repeat($maskingCharacter, strlen($number) - 8) . substr($number, -4);
    }

    function VerifyPromocode($promocode,$user_id){
        try
        {
            $check_valid_code   =   DB::table('vouchers')->where(DB::raw('BINARY `code`'),$promocode)->first();
            if(!$check_valid_code)
            {
                throw new Exception("Invalid Promo Code");
            }

            $today              =   date('Y-m-d');
            $paymentDate        =   date('Y-m-d', strtotime($today));
            $promo_start_date   =   date('Y-m-d', strtotime($check_valid_code->starts_at));
            $promo_end_date     =   date('Y-m-d', strtotime($check_valid_code->expires_at));
            if (!(($paymentDate >= $promo_start_date) && ($paymentDate <= $promo_end_date))){
                throw new Exception("Promocode Expired");
            }

            $max_use_per_user   =   $check_valid_code->user_max_uses;
            $already_used_count =   DB::table('vouchers_used')->where('user_id',$user_id)
                                    ->where('promocode',$promocode)->count();
            if($already_used_count >= $max_use_per_user){
                throw new Exception("Promocode Already Used");
            }

            $max_limit          =   $check_valid_code->max_uses;
            $promocode_use_count=   DB::table('vouchers_used')
                                    ->where('promocode',$promocode)->count();
            if($promocode_use_count >= $max_limit){
                throw new Exception("Promocode Limit Exceeds. Invalid Promocode");
            }

            $result =   ['status'=>true,'message'=>"Promocode Verified"];
        }
        catch (\Exception $e)
        {
            $result = ['status'=>false, "message"=>$e->getMessage()];
        }
        return $result;
    }

    function PromocodeUsed($promocode,$user_id,$booking_id){
        try
        {
            $used_promocode     =   DB::table('vouchers_used')->insert(['promocode'=>$promocode,
                                    'user_id'=>$user_id,'booking_id'=>$booking_id]);
            $promo_used_count   =   DB::table('vouchers_used')->where('promocode',$promocode)->count();
            $update_promocode   =   DB::table('vouchers')->where('code',$promocode)
                                    ->update(['uses'=>$promo_used_count]);

            $result =   ['status'=>true,'message'=>"Promocode Verified"];
        }
        catch (\Exception $e)
        {
            $result = ['status'=>false, "message"=>$e->getMessage()];
        }
        return $result;
    }

    function CheckToken_DoPayment($customer_email, $payment_type, $from_save_card, $cardtoken, $total_amount)
    {
        try {
            $stripe = new \Stripe\StripeClient([
                "api_key"=>env('STRIPE_SECRET'),
                "stripe_version"=> "2020-08-27"
            ]);

            if($payment_type    == "applepay")
            {
                // Create Customer Id
                $customer       =   $stripe->customers->create([
                    'description' => 'CustomerID created for UserId'.$customer_email.' to do Booking',
                ]);
                $customer_id    =   $customer->id;
                // Do Payment
                $charge = $stripe->charges->create([
                            'card' => $cardtoken,
                            'currency' => 'aed',
                            'amount' => $total_amount*100,
                            'description' => 'Testing Purpose wallet',
                            ]);
                if(!$charge)
                {
                    throw new Exception("Something Went Wrong. Please Try Later");
                }

                $charge_id  = $charge->id;
            } else {
                // Payment Type is Card
                if($from_save_card == false){
                    // Create Customer
                    $customer       =   $stripe->customers->create([
                        'description' => 'CustomerID created for UserId'.$customer_email.' to Do Booking',
                    ]);
                    $customer_id    =   $customer->id;
                } else {
                    $user_exists    =   DB::table('save_cards')->where('card_token',$cardtoken)->first();
                    $customer_id    =   $user_exists->customer_id;
                }

                // Create Payment Intent
                $create_payment_intent = $stripe->paymentIntents->create([
                        'amount' => $total_amount*100,
                        'currency' => 'aed',
                        'description' => 'Mood Booking',
                        "customer"  => $customer_id,
                        "payment_method_types" => ['card'],
                ]);

                // Confirm Payment Intent
                $confirm_payment_intent =   $stripe->paymentIntents->confirm(
                                                $create_payment_intent->id,
                                                ['payment_method' => $cardtoken]
                                            );

                $charge_id  =   $confirm_payment_intent->charges['data'][0]->id;
            }
            $result = ['status'=>true,"customer_id"=>$customer_id,"charge_id"=>$charge_id,"message"=>"Payment Done Successfully"];

        }
        catch (\Exception $e)
        {
            // "Something Went Wrong With Payment Token"
            $result = ['status'=>false, "message"=>$e->getMessage()];
        }
        return $result;
    }

    function RefundBookingPayment($charge_id,$repay_amount){
        try {
            $stripe = new \Stripe\StripeClient([
                "api_key"=>env('STRIPE_SECRET'),
                "stripe_version"=> "2020-08-27"
            ]);
            $refund =   $stripe->refunds->create([
                            'charge' => $charge_id,
                            'amount' => $repay_amount*100
                        ]);
            if(!$refund){
                throw new Exception("Payment Already Returned");
            }
            $result = ['status'=>true,"refund_id"=>$refund->id,"message"=>"Payment Cancelled Successfully"];
        }
        catch (\Exception $e)
        {
            $result = ['status'=>false, "message"=>$e->getMessage()];
        }
        return $result;
    }

}

