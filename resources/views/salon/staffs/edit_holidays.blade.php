@extends('layouts.master_salon')

@section('title')
Dashboard
@stop
@section('content')

<div class="pcoded-content">
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="page-header-title">
                    <h5>Edit Holidays</h5>
                </div>
            </div>
            <div class="col-md-4">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{env('ADMIN_URL')}}/dashboard"> <i class="fa fa-home"></i> </a>
                    </li>
                    <li class="breadcrumb-item"><a href="{{env('ADMIN_URL')}}/salon/staffs">Staffs</a></li>
                    <li class="breadcrumb-item"><a href="{{env('ADMIN_URL')}}/salon/staffs/view?id={{$staff_id}}">Details</a></li>
                    <li class="breadcrumb-item"><a href="{{env('ADMIN_URL')}}/salon/staffs/holidays?staff_id={{$staff_id}}">Holidays</a></li>
                    <li class="breadcrumb-item"><a>Edit Holidays</a></li>
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
            <h5>Edit Holiday</h5>
        </div>
        <div class="card-block">
        @include('includes.msg')

        {!! Form::open(["url"=>env('ADMIN_URL')."/salon/staffs/holidays/edit","id"=>"main","method"=>"post",'files'=> true]) !!}
            @csrf
            
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Staff</label>
            <div class="col-sm-10">
                {!! Form::text("staff",$staff,["placeholder"=>"Staff","class"=>"form-control","readonly"=>true]) !!}

                <span class="messages"></span>
            </div>
        </div>
          <div class="form-group row">
            <label class="col-sm-2 col-form-label">Choose holiday</label>
            <div class="col-sm-10">
            {!! Form::text("date",$holiday->date,["class"=>"form-control datepicker", "placeholder"=>"Choose Date","autocomplete"=>"off"]) !!}

                <span class="messages"></span>
            </div>
        </div>
      
        <div class="form-group row">
            <label class="col-sm-2"></label>
            <div class="col-sm-10">
                <!-- <button type="submit" class="btn btn-primary m-b-0">Submit</button> -->
                {!! Form::hidden('staff_id',$staff_id) !!}
                {!! Form::hidden('id',$id) !!}

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
</div>
</div>


@stop