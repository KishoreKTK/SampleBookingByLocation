<?php

namespace App\Http\Middleware;
use Auth;
use Closure;
use Session;
use App\Booking;
use App\Approvals;
use App\ContactUs;
use Illuminate\Support\Str;

class admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $booking        =   Booking::where("read_a",0)->count();
        $transactions   =   Booking::where("read_ta",0)->count();
        $contact        =   ContactUs::where("read_a",0)->count();
        $approvals      =   Approvals::where("read",0)->count();
        
        Session::put('contact', $contact);
        Session::put('approvals', $approvals);
        Session::put('booking', $booking);
        Session::put('transactions', $transactions);

        $roles=Session::get('roles');
        if($request->path()=="mdadmin/salons" || strpos($request->path(), 'mdadmin/salons/') !== false)
        {
            if (!in_array("Salons", $roles))
            {
                return redirect()->back()->with("error", true)->with("msg", "You don't have the access to view this page");
            }
            else
            {
                return $next($request);
            }
        }
        elseif($request->path()=="mdadmin/users"|| strpos($request->path(), 'mdadmin/users/') !== false)
        {
            if (!in_array("Users", $roles))
            {
                return redirect()->back()->with("error", true)->with("msg", "You don't have the access to view this page");
            }
            else
            {
                return $next($request);
            }
        }
         elseif($request->path()=="mdadmin/categories"||strpos($request->path(), 'mdadmin/categories/') !== false)
        {
            if (!in_array("Categories", $roles))
            {
                return redirect()->back()->with("error", true)->with("msg", "You don't have the access to view this page");
            }
            else
            {
            return $next($request);
            }
        }
        elseif($request->path()=="mdadmin/reviews"|| strpos($request->path(), 'mdadmin/reviews/') !== false)
        {
            if (!in_array("Reviews", $roles))
            {
                return redirect()->back()->with("error", true)->with("msg", "You don't have the access to view this page");
            }
            else
            {
            return $next($request);
            }
        }
         elseif($request->path()=="mdadmin/booking"|| strpos($request->path(), 'mdadmin/booking/') !== false)
        {
            if (!in_array("Booking", $roles))
            {
                return redirect()->back()->with("error", true)->with("msg", "You don't have the access to view this page");
            }
            else
            {
            return $next($request);
            }
        }
        //  elseif($request->path()=="billing"|| $request->path()=="billing/*")
        // {
        //     if (!in_array("Billing", $roles))
        //     {
        //         return redirect()->back()->with("error", true)->with("msg", "You don't have the access to view this page");
        //     }
        //     else
        //     {
        //     return $next($request);
        //     }
        // }
         elseif($request->path()=="mdadmin/contact_us"|| strpos($request->path(), 'mdadmin/contact_us/') !== false)
        {
            if (!in_array("Contact Us", $roles))
            {
                return redirect()->back()->with("error", true)->with("msg", "You don't have the access to view this page");
            }
            else
            {
                return $next($request);
            }
        }
        elseif($request->path()=="mdadmin/faq"|| strpos($request->path(), 'mdadmin/faq/') !== false)
        {
            if (!in_array("FAQ", $roles))
            {
                return redirect()->back()->with("error", true)->with("msg", "You don't have the access to view this page");
            }
            else
            {
                return $next($request);
            }
        }
        else
        {
            return $next($request);
        }

    }
}
