@extends('layouts.master_salon')
@section('title')
Dashboard
@stop
@section('content')
<div class="pcoded-content">
    <!-- Page-header start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="page-header-title">
                        <h5>Salon Booking</h5>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{env('ADMIN_URL')}}/salon/dashboard"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a>Booking</a></li>
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
                            <a class="btn btn-primary" href="{{ env('ADMIN_URL') }}/salon/booking/DownloadReport">Download Report</a>
                        </div>
                        <div class="card-block table-border-style">
                            <div class="row">
                                <div class="col-lg-12 col-xl-12">

                                    <!--------------------------------------------------- -->
                                    {!! Form::open(["url"=>env('ADMIN_URL')."/salon/booking","method"=>"get",
                                    "class"=>"form-material"]) !!}

                                    <div class="searchgroup">
                                        <div class="row">


                                            <div class="form-group col-md-2">
                                                <!-- <input type="text" class="form-control" id="inputEmail4" placeholder="Filter by Keyword"> -->
                                                {!!Form::text("keyword",$keyword,["class"=>"form-control","id"=>"inputEmail4",
                                                "placeholder"=>"Filter by Keyword"]) !!}
                                            </div>


                                            <div class="form-group col-md-3">

                                                @if(isset($start_date)&& $start_date!='')

                                                {!!Form::text("start_date",$start_date,["class"=>"form-control datepicker","id"=>"date", "aria-describedby"=>"emailHelp","placeholder"=>"Choose From Date","autocomplete"=>"off"]) !!}
                                                @else
                                                {!!Form::text("start_date",'',["class"=>"form-control datepicker","id"=>"date", "aria-describedby"=>"emailHelp","placeholder"=>"Choose From Date","autocomplete"=>"off"]) !!}
                                                @endif
                                            </div>
                                            <div class="form-group col-md-3">

                                                @if(isset($end_date)&& $end_date!='')
                                                {!!Form::text("end_date",$end_date,["class"=>"form-control datepicker","id"=>"date", "aria-describedby"=>"emailHelp","placeholder"=>"Choose To Date","autocomplete"=>"off"]) !!}
                                                @else
                                                {!!Form::text("end_date",'',["class"=>"form-control datepicker","id"=>"date", "aria-describedby"=>"emailHelp","placeholder"=>"Choose To Date","autocomplete"=>"off"]) !!}
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
                                            <div class="form-group col-2">
                                                <div class="btn-group" role="group" aria-label="Basic example">
                                                    <!-- <button type="button" class="btn btn-primary"><i
                                                            class="fas fa-search"></i></button>

                                                    <button type="button" class="btn btn-primary"><i
                                                            class="fas fa-times"></i></button> -->
                                                    {{ Form::button('<i class="fas fa-search"></i>', ['type' => 'submit', 'class' => 'btn btn-primary'] )  }}
                                                    <a href="{{ env('ADMIN_URL') }}/salon/booking" class='btn btn-primary'><i class="fas fa-times"></i></a>
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
                                    <table class="table table-sm table-hover">
                                        <thead>
                                            <tr>
                                                <th width="30">#</th>
                                                <!-- <th>Salon</th> -->
                                                <th>Customer Name</th>
                                                <th>Phone</th>
                                                <th>Booked On</th>
                                                <th width="100"></th>
                                                <th width="100">Status</th>
                                                <th class="text-right" width="100">Price</th>
                                                <!-- <th width="100"></th> -->
                                                <th width="100"></th>
                                                <th width="100"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($booking as $index=>$each)
                                            <tr>
                                                <th scope="row"> {{$index+$booking->firstItem() }}</th>
                                                <td>
                                                    @if($each->user_id>0)
                                                    <a href="{{env('ADMIN_URL')}}/salon/users/details?id={{$each->user_id}}">{{$each->first_name}} {{$each->last_name}}</a>
                                                    @else
                                                    {{$each->first_name}} {{$each->last_name}}
                                                    @endif
                                                </td>
                                                <td>{{$each->phone}}</td>
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
                                                <!--  @if($each->active!=2  && $each->active!=4)
                                            <td><a class="btn btn-primary d-block" style="background: color" href="{{ env('ADMIN_URL') }}/salon/booking/cancel?booking_id={{$each->id}}">Cancel</a></td>
                                            @else
                                            <td></td>
                                            @endif -->
                                                <td><a class="btn btn-primary d-block" href="{{ env('ADMIN_URL') }}/salon/booking/invoice?booking_id={{$each->id}}" target="_blank">Invoice</a>
                                                </td>
                                                <td><a class="btn btn-primary d-block" href="{{ env('ADMIN_URL') }}/salon/booking/details?booking_id={{$each->id}}">View</a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @else
                                    <table>
                                        <thead>No records found</thead>
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
