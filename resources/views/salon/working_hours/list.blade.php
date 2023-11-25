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
                        <h5>Working Hours</h5>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{env('ADMIN_URL')}}/salon/dashboard"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a>Working Hours</a>
                        </li>
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
                                <div class="card-header justify-content-between">
                                    <h5>Working Hours</h5>
                                    <a class="btn btn-primary d-block"
                                    href="{{env('ADMIN_URL')}}/salon/working_hours/edit">
                                    Update</a>
                                </div>
                                <div class="card-block">
                                    <div class="table-responsive">
                                        @include('includes.msg')
                                        <table class="table table-sm table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Sunday</td>
                                                    <td>{{$working_hours['sunday_start']}} -
                                                        {{$working_hours['sunday_end']}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Monday</td>
                                                    <td>{{$working_hours['monday_start']}} -
                                                        {{$working_hours['monday_end']}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Tuesday</td>
                                                    <td>{{$working_hours['tuesday_start']}} -
                                                        {{$working_hours['tuesday_end']}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Wednesday</td>
                                                    <td>{{$working_hours['wednesday_start']}} -
                                                        {{$working_hours['wednesday_end']}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Thursday</td>
                                                    <td>{{$working_hours['thursday_start']}} -
                                                        {{$working_hours['thursday_end']}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Friday</td>
                                                    <td>{{$working_hours['friday_start']}} -
                                                        {{$working_hours['friday_end']}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Saturday</td>
                                                    <td>{{$working_hours['saturday_start']}} -
                                                        {{$working_hours['saturday_end']}}</td>
                                                </tr>

                                            </tbody>
                                        </table>
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
