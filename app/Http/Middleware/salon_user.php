<?php

namespace App\Http\Middleware;
use Auth;
use Closure;
use Session;

class salon_user
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
        $sroles=Session::get('sroles');
       
        if($request->path()=="salon/booking" || $request->path()=="salon/booking*")
        {
            if (!in_array("Booking", $sroles))
            {
                return redirect()->back()->with("error", true)->with("msg", "You don't have the access to view this page");
            }
            else
            {
            return $next($request);
            }
        }
        elseif($request->path()=="salon/block" || $request->path()=="salon/block*" || $request->path()=="salon/list_block" || $request->path()=="salon/list_block*")
        {
            if (!in_array("Block Slots", $sroles))
            {
                return redirect()->back()->with("error", true)->with("msg", "You don't have the access to view this page");
            }
            else
            {
            return $next($request);
            }
        }
        elseif($request->path()=="salon/services"|| $request->path()=="salon/services/*")
        {
            if (!in_array("Services", $sroles))
            {
                return redirect()->back()->with("error", true)->with("msg", "You don't have the access to view this page");
            }
            else
            {
            return $next($request);
            }
        }
         elseif($request->path()=="salon/staffs"|| $request->path()=="salon/staffs/*")
        {
            if (!in_array("Staff", $sroles))
            {
                return redirect()->back()->with("error", true)->with("msg", "You don't have the access to view this page");
            }
            else
            {
            return $next($request);
            }
        }
        elseif($request->path()=="salon/working_hours"|| $request->path()=="salon/working_hours/*")
        {
            if (!in_array("Working Hours", $sroles))
            {
                return redirect()->back()->with("error", true)->with("msg", "You don't have the access to view this page");
            }
            else
            {
            return $next($request);
            }
        }
         elseif($request->path()=="salon/reviews"|| $request->path()=="salon/reviews/*")
        {
            if (!in_array("Reviews", $sroles))
            {
                return redirect()->back()->with("error", true)->with("msg", "You don't have the access to view this page");
            }
            else
            {
            return $next($request);
            }
        }
         elseif($request->path()=="salon/categories"|| $request->path()=="salon/categories/*")
        {
            if (!in_array("Categories", $sroles))
            {
                return redirect()->back()->with("error", true)->with("msg", "You don't have the access to view this page");
            }
            else
            {
            return $next($request);
            }
        }
         elseif($request->path()=="salon/transactions"|| $request->path()=="salon/transactions/*")
        {
            if (!in_array("Transactions", $sroles))
            {
                return redirect()->back()->with("error", true)->with("msg", "You don't have the access to view this page");
            }
            else
            {
            return $next($request);
            }
        }
         elseif($request->path()=="salon/salon_users"|| $request->path()=="salon/salon_users/*")
        {
            if (!in_array("Salon Users", $sroles))
            {
                return redirect()->back()->with("error", true)->with("msg", "You don't have the access to view this page");
            }
            else
            {
            return $next($request);
            }
        }
         elseif($request->path()=="salon/schedules"|| $request->path()=="salon/schedules/*")
        {
            if (!in_array("Schedules", $sroles))
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
