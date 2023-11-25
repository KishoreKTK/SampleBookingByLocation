<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class MoodController extends Controller
{
    //
    public function JoinMoodForm(){
        $category_list =    DB::table('categories')->select('id','category')
                            ->where('active_status',1)->whereNull('deleted_at')->get();
        return view('website.joinmood',compact('category_list'));
    }

    public function PostJoinMoodEnquiry()
    {
        $input  =   request()->all();
        if(request()->has('pricesheet') && request()->pricesheet){
            $enquerySheet                   =   'Uploads/Enquiry/';
            $image_name                     =   time() . '.'.request()->file('pricesheet')->getClientOriginalExtension();

            // Move File to Folder
            request()->pricesheet->move(public_path($enquerySheet), $image_name);

            $input['price_sheet']           =   $enquerySheet.$image_name;

        }

        $input['category']          =   implode(',', $input['categories']);
        $input['enquired_date']     =   date('Y-m-d H:i:s');

        unset($input['pricesheet']);
        unset($input['categories']);
        unset($input['_token']);

        // dd($input);
        $enquiry_id                     =   DB::table('enquiry_list')->insertGetId($input);
        if($enquiry_id)
        {
            $category_ids               =   $input['category'];
            $catid_arr                  =   explode(',',$category_ids);
            $cat_names_arr              =   DB::table('categories')->whereIn('id',$catid_arr)
                                            ->pluck('category')->toArray();
            $input['category_names']    =   implode(',',$cat_names_arr);

            Mail::send('emails.new_enquiry', $input, function ($message) use($input) {
                // $message->from($input['email'], $input['user_name']);
                $message->to('hello@moodapp.ae', 'Mood App');
                $message->subject('Join Mood Enquiry');
                $message->attach($input['price_sheet'], [
                                    'as' => 'Price Sheet.pdf',
                                    'mime' => 'application/pdf',
                            ]);
            });
            return redirect()->back()->with('success','Thank you for submitting the form! Our business team will review you application and get back to you soon.');
            // ->route('homepage')
        }
        else{
            return redirect()->back()->with('error','Something Went Wrong Please Try Again Later');
        }
    }

    public function EnquiryList()
    {
        $enquiry_list   =   DB::table('enquiry_list')->orderBy('enquired_date','desc')->paginate(50);
        // dd($enquiry_list);
        return view('admin.Enquiry.EnquiryList',compact('enquiry_list'));
    }
}
