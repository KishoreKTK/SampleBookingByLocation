@extends('layouts.master_salon')

@section('title')
Dashboard
@stop
@section('content')
 <script type="text/javascript" src="{{url('/')}}/public/assets/files/bower_components/jquery/js/jquery.min.js "></script>
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
                        <li class="breadcrumb-item">
                        <a href="{{env('ADMIN_URL')}}/salon/booking">Booking</a>
                        </li>
                          <li class="breadcrumb-item">
                        <a href="{{env('ADMIN_URL')}}/salon/booking/add">Add</a>
                        </li>
                        <li class="breadcrumb-item"><a>Complete Booking</a>
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
                  <div class="card-header">
                      <h5>Add Booking</h5>
                  </div>
                 

                <div class="card-block">

                {!! Form::open(["url"=>env('ADMIN_URL')."/salon/booking/complete","id"=>"main","method"=>"post",'files'=> true]) !!}
                      @csrf
                @include('includes.msg')

                <div class="form-group row">
                  <label class="col-sm-2 col-form-label">User</label>
                  <div class="col-sm-10">
                    {!! Form::select("user_id",$users,null,["class"=>"form-control", "placeholder"=>"Choose User"]) !!}
                      <span class="messages"></span>
                  </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">First Name</label>
                    <div class="col-sm-10">
                        {!! Form::text("first_name",'',["placeholder"=>"First Name","id"=>"name","class"=>"form-control"]) !!}

                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Last Name</label>
                    <div class="col-sm-10">
                        {!! Form::text("last_name",'',["placeholder"=>"Last Name","id"=>"name","class"=>"form-control"]) !!}

                    </div>
                </div>
                 <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                        {!! Form::text("email",'',["placeholder"=>"Email","id"=>"name","class"=>"form-control"]) !!}

                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Phone</label>
                    <div class="col-sm-10">
                        {!! Form::text("phone",'',["placeholder"=>"Phone","id"=>"name","class"=>"form-control"]) !!}

                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Address</label>
                    <div class="col-sm-10">
                        {!! Form::text("address",'',["placeholder"=>"Address","id"=>"name","class"=>"form-control"]) !!}

                    </div>
                </div>
                 <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Amount(in AED)</label>
                    <div class="col-sm-10">
                        {!! Form::text("amount",'',["placeholder"=>"Amount","id"=>"name","class"=>"form-control",'required' => 'required']) !!}

                        <span class="messages"></span>
                    </div>
                </div>
                  <div class="form-group row">
                      <label class="col-sm-2"></label>
                      <div class="col-sm-10">
                          {!! Form::hidden('date',$date) !!}
                          {!! Form::hidden('salon_id',$salon_id) !!}
                          {!! Form::hidden('service_id',$service_id) !!}
                          {!! Form::hidden('start_time',$start_time) !!}
                          {!! Form::hidden('end_time',$end_time) !!}
                          {!! Form::hidden('staff_id',$staff_id) !!}
                          <!-- <button type="submit" class="btn btn-primary m-b-0">Submit</button> -->
                           {!! Form::submit('Book',["class"=>"btn btn-primary m-b-0"]) !!}
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
  