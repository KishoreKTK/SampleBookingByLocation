<?php

namespace App\Http\Controllers;
use DB;
use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminSchedulesController extends Controller
{
    public function index(Request $request)
    {
        $activePage="Schedules";
    	return view("admin.schedules.list",compact('activePage'));
    }

}
