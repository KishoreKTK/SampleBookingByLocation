<?php

namespace App\Http\Controllers;

use App\Http\Traits\PaymentTrait;
use App\UserToken;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Http\Request;

class AdminCoupenController extends Controller
{
    use PaymentTrait;
    //
    public function index(){
        $promocodes  = DB::table('vouchers')->whereNull('deleted_at')->get();
        return view('admin.coupens.list',compact('promocodes'));
    }

    public function add() {
        return view('admin.coupens.add');
    }

    public function add_promocode()
    {
        try
        {
            $rules=
            [
                "code"=>"required|min:3|unique:vouchers,code",
                "name"=>"min:3",
                "starts_at"=>"required",
                "expires_at"=>"required",
            ];

            $msg=
            [
                "starts_at.required"=>"Start Date is empty",
                "expires_at.required"=>"Expiry Date is empty",
            ];
            $validator=Validator::make(request()->all(),$rules,$msg);
            if ($validator->fails())
            {
                return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
            } else{

                // dd(request()->all());
                // $promonamestr = str_replace(' ', '', request()->name);
                // $first_three_char = substr($promonamestr,0,2);
                // $length  =   4;
                // $get_records     =   DB::table('vouchers')->get();
                // if(count($get_records) == 0) {
                //     $id     =   1;
                // } else {
                //     $code       =   DB::table('vouchers')->latest('created_at')->first()->code;
                //     // dd($code);
                //     $get_id     =   substr($code,2,1);
                //     $id         =   (int)$get_id + 1;
                // }

                // $promocode  =   $this->coupon($first_three_char,$id,$length);

                $input                  =   request()->except('_token');
                $input['starts_at']     =   date('Y-m-d', strtotime(request()->starts_at));
                $input['expires_at']    =   date('Y-m-d', strtotime(request()->expires_at));
                $input['created_at']    =   date('Y-m-d H:i:s');
                $input['deleted_at']    =   null;
                $voucher_id    =    DB::table('vouchers')->insertGetId($input);
                if(!$voucher_id){
                    throw new Exception("Something Went Wrong");
                }
                $result =   ['status'=>true,'message'=>'Coupen Created Succesfully'];
            }
        } catch(Exception $e){
            $result =   ['status'=>false,'message'=>$e->getMessage()];
        }
        if($result['status'] == false){
            return redirect()->back()->with("error", true)->with("msg", $result['message'])->withInput();
        }
        return redirect()->route('coupenlist')->with("error", false)->with("msg", $result['message'])->withInput();
    }


    function coupon($str,$id,$len)
    {
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $randomval  = substr(str_shuffle($str_result),0, $len);
		$coupon     = $str.$id.$randomval;
		return $coupon;
	}


    public function CheckPromocodeValidation()
    {
        try
        {
            $api_token  =   request()->header('User-Token');
	        $user       =   UserToken::where("api_token",$api_token)->first();
            if(!$user){
                throw new Exception("Please register to Use PromoCode");
            }
            if(request()->filled('amount') && request()->filled('promocode'))
            {
                $user_id            =   $user->user_id;
                $promocode          =   request()->promocode;
                $verifypromocode    =   $this->VerifyPromocode($promocode,$user_id);
                if($verifypromocode['status'] == false){
                    throw new Exception($verifypromocode['message']);
                }
                $booking_amount     =   request()->amount;
                $check_valid_code   =   DB::table('vouchers')->where('code',$promocode)->first();
                if($check_valid_code->type == 1){
                    $promo_discount     =   $check_valid_code->discount;
                    $discount_text      =   $promo_discount." %";
                    $discount_amount    =   $booking_amount * (100 - $promo_discount)/  100;
                    $amount_saved       =   round($booking_amount*($promo_discount/100));
                } else {
                    $promo_discount     =   $check_valid_code->discount;
                    $discount_text      =   $promo_discount." AED";
                    $amount_saved       =   $promo_discount;
                    $discount_amount    =   $booking_amount - $promo_discount;
                }
                $result =   [
                                'status'=>true,
                                "booking_amount"=>$booking_amount,
                                "discount"=>$discount_text,
                                'diffrence_amount'=>$amount_saved,
                                "new_payment_amount"=>round($discount_amount),
                                'message'=>"Promocode Applied"
                            ];
                // if(!$check_valid_code)
                // {
                //     throw new Exception("Invalid Promo Code");
                // }
                // $today              =   date('Y-m-d');
                // $paymentDate        =   date('Y-m-d', strtotime($today));
                // $promo_start_date   =   date('Y-m-d', strtotime($check_valid_code->starts_at));
                // $promo_end_date     =   date('Y-m-d', strtotime($check_valid_code->expires_at));
                // VerifyPromocode($promocode,$user_id);

                // if (($paymentDate >= $promo_start_date) && ($paymentDate <= $promo_end_date)){
                //     $promo_code_usage_count =   DB::table('vouchers_used')
                //                                 ->where('promocode',$promocode)->count();

                //     if($promo_code_usage_count > $check_valid_code->max_uses){
                //         throw new Exception("Promo code limit exceeded");
                //     }

                //     $check_vouchers_used    =   DB::table('vouchers_used')->where('user_id',$user_id)
                //                                 ->where('promocode',$promocode)->count();
                //     if($check_vouchers_used > $check_valid_code->user_max_uses){
                //         throw new Exception("You have already used this Promocode");
                //     }
                // }else{
                //     throw new Exception("Promocode Expired");
                // }
            } else {
                throw new Exception("Amount or Promocode is Missing. Check Input");
            }
         } catch(Exception $e){
            $result =   ['status'=>false,'message'=>$e->getMessage()];
        }
        return response()->json($result);
    }

    public function deletepromocode()
    {
        DB::table('vouchers')->where('id',request()->get('id'))->delete();
        return redirect()->back()->with("error", false)->with("msg", "Deleted Successfully")->withInput();
    }

}
