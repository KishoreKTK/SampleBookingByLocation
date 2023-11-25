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
                        <h5>Add Holidays</h5>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{env('ADMIN_URL')}}/dashboard"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{env('ADMIN_URL')}}/salon/staffs">Staffs</a></li>
                        {{-- <li class="breadcrumb-item"><a href="{{env('ADMIN_URL')}}/salon/staffs/view?id={{$staff_id}}">Details</a></li> --}}
                        <li class="breadcrumb-item"><a>Add Holidays</a></li>
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
                                    <h5>Add Holiday</h5>
                                </div>
                                <div class="card-block">
                                    @include('includes.msg')

                                    {!! Form::open(["url"=>env('ADMIN_URL')."/salon/staffs/holidays/add","id"=>"main","method"=>"post",'files'=> true]) !!}
                                    @csrf

                                     <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Staff</label>
                                        <div class="col-sm-10">
                                            {!! Form::text("staff",$staff,["placeholder"=>"Staff","class"=>"form-control","readonly"=>true]) !!}

                                            <span class="messages"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Add holiday</label>
                                        <div class="col-sm-10">
                                            {!! Form::text("date",'',["class"=>"form-control multi-flatpickr","placeholder"=>"Choose Date","autocomplete"=>"off"]) !!}

                                            <span class="messages"></span>
                                        </div>
                                    </div>
                                  <!--   <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Add closed dates</label>
                                        <div class="col-sm-10">
                                            {!! Form::text("date",'',["class"=>"form-control", "id"=>"multidatepicker", "placeholder"=>"Choose Date","autocomplete"=>"off"]) !!}

                                            <span class="messages"></span>
                                        </div>
                                    </div>
 -->
                                    <div class="form-group row">
                                        <label class="col-sm-2"></label>
                                        <div class="col-sm-10">
                                            <!-- <button type="submit" class="btn btn-primary m-b-0">Submit</button> -->
                                            {!! Form::hidden('staff_id',$staff_id) !!}

                                            {!! Form::submit('Add',["class"=>"btn btn-primary m-b-0"]) !!}
                                        </div>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                                <div class="card-block">

                                    <div class="table-responsive">
                                        <div class="table-content">
                                            <div class="project-table">
                                                <table class="table table-sm table-hover">
                                                    @if(isset($holidays)&& count($holidays)>0)
                                                    <thead>
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Created at</th>
                                                            <th></th>
                                                            <th width="100">Action</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @foreach($holidays as $holiday)
                                                        <tr>

                                                            <td class="pro-name">
                                                                <h6>{{$holiday->date}}</h6>
                                                                <!-- <span>Lorem ipsum dolor sit consec te imperdiet iaculis ipsum..</span> -->
                                                            </td>
                                                            <td>{{$holiday->created_at->format('d-m-Y g:i A')}}</td>
                                                            <td></td>
                                                            <td class="action-icon">

                                                                <a href="{{env('ADMIN_URL')}}/salon/staffs/holidays/edit?id={{$holiday->id}}&staff_id={{$staff_id}}" class="m-r-15 text-muted" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>
                                                                <a href="{{env('ADMIN_URL')}}/salon/staffs/holidays/delete?id={{$holiday->id}}&staff_id={{$staff_id}}" class="text-muted" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i class="icofont icofont-delete-alt"></i></a>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                    @else
                                                    <tr>
                                                        <td>No records found</td>
                                                    </tr>
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </div>
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
