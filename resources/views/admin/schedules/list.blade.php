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
                        <h5>Schedules</h5>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{env('ADMIN_URL')}}/dashboard"> <i class="fa fa-home"></i> </a>
                            </li>
                            <li class="breadcrumb-item"><a>Schedule</a>
                            </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- Page-header end -->


    <div class="pcoded-inner-content full-calender">
        <div class="main-body">
            <div class="page-wrapper">
                
                <div class="page-body">
                    <div class="card">
                        <div class="card-header">
                            <h5>Schedule</h5>
                        </div>
                        <div class="card-block">
                            <div class="row">
                              
                                <div class="col-xl-12 col-md-12">
                                    <div id='calendar'></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--    <div class="page-error">
            <div class="card text-center">
                <div class="card-block">
                    <div class="m-t-10">
                        <i class="icofont icofont-warning text-white bg-c-yellow"></i>
                        <h4 class="f-w-600 m-t-25">Not supported</h4>
                        <p class="text-muted m-b-0">Full Calender not supported in this device</p>
                    </div>
                </div>
            </div>
        </div> -->
    </div>
</div>

@stop