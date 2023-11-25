@extends('layouts.master')

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
                    <h5>Salons</h5>
                </div>
            </div>
            <div class="col-md-4">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{env('ADMIN_URL')}}/dashboard"> <i class="fa fa-home"></i> </a>
                    </li>
                    <li class="breadcrumb-item"><a href="{{env('ADMIN_URL')}}/salons">Salons</a></li>
                    <li class="breadcrumb-item"><a  href="{{env('ADMIN_URL')}}/salons/details?id={{$salon_id}}">Details</a></li>
                    <li class="breadcrumb-item"><a>Edit Working Hours</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
    <!-- Page-header end -->
<div class="pcoded-inner-content">
<div class="main-body">
<div class="page-wrapper">
    <!-- Page body start -->
    <div class="page-body">
    <div class="row">
    <div class="col-sm-12">
        <!-- Product list card start -->
    <div class="card">
        <div class="card-header">
            <h5>Edit Working Hours</h5>
        </div>
        <div class="card-block">
        @include('includes.msg')

        {!! Form::open(["url"=>env('ADMIN_URL')."/salons/working_hours/edit","id"=>"main","method"=>"post",'files'=> true]) !!}
            @csrf
     
          <div class="form-group row">
            <label  class="col-sm-2 col-control-label">Sunday start time</label>
            <div class="col-sm-4">
            {!! Form::time("sunday_start",$time['sunday_start'],["class"=>"form-control", "placeholder"=>"Sunday start time"]) !!}

            </div>
             <label  class="col-sm-2 col-control-label">Sunday end time</label>
            <div class="col-sm-4">
            {!! Form::time("sunday_end",$time['sunday_end'],["class"=>"form-control", "placeholder"=>"Sunday end time"]) !!}

            </div>
          </div>
          
           <div class="form-group row">
            <label  class="col-sm-2 col-control-label">Monday start time</label>
            <div class="col-sm-4">
            {!! Form::time("monday_start",$time['monday_start'],["class"=>"form-control", "placeholder"=>"Monday start"]) !!}

            </div>
             <label  class="col-sm-2 col-control-label">Monday end time</label>
            <div class="col-sm-4">
            {!! Form::time("monday_end",$time['monday_end'],["class"=>"form-control", "placeholder"=>"Monday end time"]) !!}

            </div>
          </div>
        
           <div class="form-group row">
            <label  class="col-sm-2 col-control-label">Tuesday start time</label>
            <div class="col-sm-4">
            {!! Form::time("tuesday_start",$time['tuesday_start'],["class"=>"form-control", "placeholder"=>"Tuesday start"]) !!}

            </div>
             <label  class="col-sm-2 col-control-label">Tuesday end time</label>
            <div class="col-sm-4">
            {!! Form::time("tuesday_end",$time['tuesday_end'],["class"=>"form-control", "placeholder"=>"Tuesday end time"]) !!}

            </div>
          </div>
        
           <div class="form-group row">
            <label  class="col-sm-2 col-control-label">Wednesday start</label>
            <div class="col-sm-4">
            {!! Form::time("wednesday_start",$time['wednesday_start'],["class"=>"form-control", "placeholder"=>"Wednesday start"]) !!}

            </div>
            <label  class="col-sm-2 col-control-label">Wednesday end time</label>
            <div class="col-sm-4">
            {!! Form::time("wednesday_end",$time['wednesday_end'],["class"=>"form-control", "placeholder"=>"Wednesday end time"]) !!}

            </div>
          </div>
         
           <div class="form-group row">
            <label  class="col-sm-2 col-control-label">Thursday start time</label>
            <div class="col-sm-4">
            {!! Form::time("thursday_start",$time['thursday_start'],["class"=>"form-control", "placeholder"=>"Thursday start time"]) !!}

            </div>
              <label  class="col-sm-2 col-control-label">Thursday end time</label>
            <div class="col-sm-4">
            {!! Form::time("thursday_end",$time['thursday_end'],["class"=>"form-control", "placeholder"=>"Thursday end time"]) !!}

            </div>
          </div>
         
           <div class="form-group row">
            <label  class="col-sm-2 col-control-label">Friday start time</label>
            <div class="col-sm-4">
            {!! Form::time("friday_start",$time['friday_start'],["class"=>"form-control", "placeholder"=>"Friday start time"]) !!}

            </div>
             <label  class="col-sm-2 col-control-label">Friday end time</label>
            <div class="col-sm-4">
            {!! Form::time("friday_end",$time['friday_end'],["class"=>"form-control", "placeholder"=>"Friday end"]) !!}

            </div>
          </div>
          <div class="form-group row">
            <label  class="col-sm-2 col-control-label">Saturday start time</label>
            <div class="col-sm-4">
            {!! Form::time("saturday_start",$time['friday_start'],["class"=>"form-control", "placeholder"=>"Saturday start time"]) !!}

            </div>
             <label  class="col-sm-2 col-control-label">Saturday end time</label>
            <div class="col-sm-4">
            {!! Form::time("saturday_end",$time['friday_end'],["class"=>"form-control", "placeholder"=>"Saturday end"]) !!}

            </div>
          </div>
      
        <div class="form-group rowrow">
            <label class="col-sm-2"></label>
            <div class="col-sm-10">
                <!-- <button type="submit" class="btn btn-primary m-b-0">Submit</button> -->
                {!! Form::hidden('salon_id',$salon_id) !!}

                 {!! Form::submit('Update',["class"=>"btn btn-primary m-b-0"]) !!}
            </div>
        </div>
         {!! Form::close() !!}
        </div>
    </div>
        <!-- Product list card end -->
    </div>
    </div>

        <!-- Add Contact Ends Model end-->
    </div>
    <!-- Page body end -->
</div>
</div>
</div>
</div>

@stop