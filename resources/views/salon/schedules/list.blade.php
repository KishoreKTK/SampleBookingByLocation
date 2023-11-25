@extends('layouts.master_salon')

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
                    <h5>Schedules</h5>
                            

                </div>
            </div>
            <div class="col-md-4">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{env('ADMIN_URL')}}/salon/dashboard"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a>Schedule</a>
                        </li>
                </ul>
            </div>
        </div>
    </div>
</div>

      


<div class="pcoded-inner-content full-calender">
      

    <div class="main-body"  >

       <!--  <div class="page-wrapper" >
                  {!! Form::open(["url"=>"salon/schedules","method"=>"get", "class"=>"form-horizontal",'files'=> true])
                !!}
                <div class="form-group row">
                    <label class="col-sm-2 col-control-label">Choose staff</label>
                    <div class="col-sm-8">
                        {!! Form::select('staff_id',$staffs,$staff_id,["id" => "category", "class"=>"form-control",
                        "placeholder"=>"Choose staff"]) !!}
                    </div>
                    <div class="col-sm-2">
                        {!! Form::submit('Choose',["class"=>"btn btn-primary"]) !!}
                        
                    </div>
                </div>

            {!! Form::close() !!}


        </div> -->
        <div class="page-wrapper">
            <div class="page-body" ng-app="myapp"  ng-controller="schedulController">
        <div ng-init="salon_id = '{{$salon_id}}'"></div>
        <div ng-init="staff_id = '{{$staff_id}}'"></div>

                <div class="card">
                    <div class="card-header justify-content-between">
                        <h5  class="align-self-center">Schedule</h5>
                        <!-- <a href="{{ env('ADMIN_URL') }}/salon/booking/block" class="btn btn-primary align-self-center">Add Booking</a> -->
                    </div>
                    <div class="card-block">
                        <div class="row">
                          
                            <div class="col-xl-12 col-md-12">
                                <div id='calendar-drag'></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</div>

@stop