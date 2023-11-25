@extends('layouts.master')

@section('title')
Dashboard
@stop
@section('content')
<!-- <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.11.0/jquery-ui.js"></script> -->
<div class="pcoded-content">
    <!-- Page-header start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="page-header-title">
                        <h5>Salon Booking</h5>

                        {{-- <a href="{{url('mdadmin/transactions/export')}}"
                            class="btn btn-sm btn-primary float-right">
                            Download Report
                        </a> --}}
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{env('ADMIN_URL')}}/dashboard"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a>Booking</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- Page-header end -->
    <div class="pcoded-inner-content">
        <!-- Main-body start -->
        <div class="main-body">
            <div class="page-wrapper">
                <!-- Page-body start -->
                <div class="page-body">
                    <!-- Horizontal-border table start -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h5>Salon Booking</h5>
                            @if(count($booking)>0)
                                <a class="btn btn-primary" href="{{ env('ADMIN_URL') }}/booking/bookingreport">Download Report</a>
                            @endif
                        </div>
                        <div class="card-block table-border-style">
                            <div class="row">
                                <div class="col-lg-12 col-xl-12">

                                    <!--------------------------------------------------- -->
                                    {!! Form::open(["url"=>env('ADMIN_URL')."/booking","method"=>"get",
                                    "class"=>"form-material"]) !!}

                                    <div class="searchgroup">
                                        <div class="row">

                                         <!--    <div class="form-group col-md-3">

                                                 <input type="text" class="form-control datepicker" id="" placeholder="Choose From Date" aria-describedby="emailHelp" >

                                            </div> -->
                                            <div class="form-group col-md-2">
                                                <!-- <select id="inputState" class="form-control">
                                                    <option selected>Filter by Salon...</option>
                                                    <option>...</option>
                                                </select> -->
                                                @if(isset($salon_id)&& $salon_id>0)

                                                {!! Form::select('salon_id',$salon,$salon_id,["class"=>"form-control",
                                                "placeholder"=>"Filter by Salon","id"=>"inputState"]) !!}
                                                @else
                                                {!! Form::select('salon_id',$salon,null,["class"=>"form-control",
                                                "placeholder"=>"Filter by Salon","id"=>"inputState"]) !!}
                                                @endif
                                            </div>


                                            <div class="form-group col-md-2">
                                                <!--  <select id="inputState" class="form-control">
                                                    <option selected>Filter by Status</option>
                                                    <option>...</option>
                                                </select> -->
                                                @if(isset($status_id)&& $status_id>=0)

                                                {!! Form::select('status_id',$status,$status_id,["id"=>"inputState",
                                                "class"=>"form-control", "placeholder"=>"Filter by Status"]) !!}
                                                @else
                                                {!! Form::select('status_id',$status,null,["id"=>"inputState",
                                                "class"=>"form-control", "placeholder"=>"Filter by Status"]) !!}
                                                @endif
                                            </div>
                                            <div class="form-group col-md-3">
                                                <!-- <input type="text" class="form-control dropper-default" id="inputEmail4"
                                                    placeholder="From Date"> -->
                                                @if(isset($start_date)&& $start_date!='')
                                                  {!!Form::text("start_date",$start_date,["class"=>"form-control datepicker","id"=>"", "aria-describedby"=>"emailHelp","placeholder"=>"Choose From Date","autocomplete"=>"off"]) !!}

                                                @else
                                                  {!!Form::text("start_date",'',["class"=>"form-control datepicker","id"=>"", "aria-describedby"=>"emailHelp","placeholder"=>"Choose From Date","autocomplete"=>"off"]) !!}


                                                @endif
                                            </div>
                                            <div class="form-group col-md-3">
                                                <!-- <input type="text" class="form-control dropper-default" id="inputEmail4" placeholder="To Date"> -->
                                                @if(isset($end_date)&& $end_date!='')
                                                 {!!Form::text("end_date",$end_date,["class"=>"form-control datepicker","id"=>"date", "aria-describedby"=>"emailHelp","placeholder"=>"Choose To Date","autocomplete"=>"off"]) !!}

                                                @else
                                                 {!!Form::text("end_date",'',["class"=>"form-control datepicker","id"=>"date", "aria-describedby"=>"emailHelp","placeholder"=>"Choose To Date","autocomplete"=>"off"]) !!}
                                                @endif
                                            </div>
                                            <div class="form-group col-2">
                                                <div class="btn-group" role="group" aria-label="Basic example">
                                                    {{ Form::button('<i class="fas fa-search"></i>', ['type' => 'submit', 'class' => 'btn btn-primary'] )  }}
                                                    <a href="{{ env('ADMIN_URL') }}/booking" class='btn btn-primary'><i
                                                            class="fas fa-times"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {!! Form::close() !!}

                                    <!--------------------------------------------------- -->

                                    <div class="mail-box-head row ">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="table-responsive">
                                    @include('includes.msg')
                                    @if(isset($booking)&& count($booking)>0)
                                        <table class="table table-sm table-hover table-framed">
                                            <thead>
                                                <tr>
                                                    <th width="35">#</th>
                                                    <th>Salon</th>
                                                    <th>Username</th>
                                                    <th>Booked On</th>
                                                    <th width="100"></th>
                                                    <th width="100">Status</th>
                                                    <th class="text-right" width="100">Price</th>
                                                    <th width="100"></th>
                                                    <th width="100"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($booking as $index=>$each)
                                                <tr>
                                                    <th scope="row"> {{$index+$booking->firstItem() }}</th>
                                                    <td>{{$each->name}}</td>
                                                    <td>
                                                        <a href="{{ env('ADMIN_URL') }}/booking/details?booking_id={{$each->id}}">{{$each->first_name}} {{$each->last_name}}</a>
                                                    </td>
                                                    <td>{{$each->created_at}}</td>
                                                    <td>
                                                        @if($each->active == 1 && $each->booking_dt_status == "Upcoming")
                                                        <span class="badge btn-primary">Upcoming</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($each->active==1)
                                                        <a class="btn btn-success btn-block">Success</a>
                                                        @elseif($each->active==2)
                                                        <a class="btn btn-danger btn-block">Cancelled</a>
                                                        @elseif($each->active==3)
                                                        <a class="btn btn-success btn-block">Processed</a>
                                                        @elseif($each->active==4)
                                                        <a class="btn btn-danger btn-block">Rejected</a>
                                                        @else
                                                        <a class="btn btn-warning btn-block">Pending</a>
                                                        @endif
                                                    </td>
                                                    <td class="text-right">{{$each->amount}} AED</td>
                                                    <td><a class="btn btn-primary btn-block"
                                                            href="{{ env('ADMIN_URL') }}/booking/invoice?booking_id={{$each->id}}" target="_blank">Invoice</a>
                                                    </td>
                                                    <td><a class="btn btn-primary btn-block"
                                                            href="{{ env('ADMIN_URL') }}/booking/details?booking_id={{$each->id}}">View</a>
                                                    </td>
                                                    {{-- @if($each->active!=2 && $each->active!=4 && $each->cancel==true)
                                                    <td><a class="btn btn-primary d-block" style="background: color"
                                                            href="{{ env('ADMIN_URL') }}/booking/cancel?booking_id={{$each->id}}">Cancel</a>
                                                    </td>
                                                    @else
                                                    <td></td>
                                                    @endif --}}
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>No records found</th>
                                            </tr>
                                        </thead>
                                    </table>
                                    @endif
                                    <center>
                                        {!! $booking->appends(Illuminate\Support\Facades\Request::except('page'))->links()
                                        !!}
                                    </center>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Horizontal-border table end -->

                </div>
                <!-- Page-body end -->
            </div>
        </div>
        <!-- Main-body end -->

    </div>
</div>
</div>


@stop
