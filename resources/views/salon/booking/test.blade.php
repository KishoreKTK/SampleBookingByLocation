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
                        <li class="breadcrumb-item"><a>Add</a>
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

                {!! Form::open(["url"=>env('ADMIN_URL')."/salon/booking/search_time","id"=>"main","method"=>"post",'files'=> true]) !!}
                      @csrf
                     @include('includes.msg')

                <div class="form-group row">
                      <label class="col-sm-2 col-form-label">Service</label>
                      <div class="col-sm-10">
                          @if(isset($service_id))

                          {!! Form::select("service_id",$services,$service_id,["id" => "service","class"=>"form-control","placeholder"=>"Choose service",'required' => 'required']) !!}
                          @else
                          {!! Form::select("service_id",$services,null,["id" => "service","class"=>"form-control","placeholder"=>"Choose service",'required' => 'required']) !!}
                          @endif

                          <span class="messages"></span>
                      </div>
                  </div>
                   <div class="form-group row">
                  <label class="col-sm-2 col-form-label">Choose date</label>

                  <div class="col-sm-10">
                      @if(isset($date))

                      {!! Form::text("date",$date,[ "id"=>"dropper-default","placeholder"=>"Choose date","class"=>"form-control",'required' => 'required']) !!}
                      @else
                      {!! Form::text("date",'',[ "id"=>"dropper-default","placeholder"=>"Choose date","class"=>"form-control",'required' => 'required']) !!}
                      @endif
                      <span class="messages"></span>

                  </div>
                  </div>
                
                <div class="form-group row">
                      <label class="col-sm-2 col-form-label">Staff</label>
                      <div class="col-sm-10">
                          {!! Form::select("staff_id",$staffs,null,["id" => "staffs","class"=>"form-control",'required' => 'required',"placeholder"=>"Choose Staff"]) !!}

                          <span class="messages"></span>
                      </div>
                  </div>
                  <div class="form-group row">
                      <label class="col-sm-2"></label>
                      <div class="col-sm-10">
                          <!-- <button type="submit" class="btn btn-primary m-b-0">Submit</button> -->
                           {!! Form::submit('Submit',["class"=>"btn btn-primary m-b-0"]) !!}
                      </div>
                  </div>

              </div>
                <div class="card-block">
                @if(isset($timeframes) && count($timeframes)>0)
                <table class="table table-sm table-hover">

                @foreach($timeframes as $index=>$each)

                 <tr>
              {!! Form::open(["url"=>env('ADMIN_URL')."/salon/booking/add","method"=>"post", "class"=>"form-horizontal row-border",'files'=> true]) !!}

                  <td>{{ $index+1  }}</td>
                  <td>
                    {!! Form::text("start_time",$each["start_time"],["class"=>"form-control", "placeholder"=>"Start time", 'readonly' => 'true']) !!}
                  </td>
                   <td>
                    {!! Form::text("end_time",$each["end_time"],["class"=>"form-control", "placeholder"=>"End time", 'readonly' => 'true']) !!}
                  </td>

                  <td> 
                    {!! Form::hidden('date',$date) !!}
                    {!! Form::hidden('salon_id',$salon_id) !!}
                    {!! Form::hidden('service_id',$service_id) !!}
                    {!! Form::hidden('staff_id',$staff_id) !!}
                  {!! Form::submit('Book',["class"=>"btn-primary btn"]) !!}</td>
                {!! Form::close() !!}

              </tr>
                @endforeach
              </table>
                @endif
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
<script>

      $('#service').on('change', function(e){
        var service_id = e.target.value;

        $.get('search_staffs?service_id=' + service_id, function(data){
            $('#staffs').empty();

            $.each(data, function(index, subcatObj){

                $('#staffs').append('<option value ="'+ index +'">'+subcatObj+'</option>');

            });
        });
    });
</script>

@stop
