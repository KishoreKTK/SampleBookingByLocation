@extends('layouts.master_salon')
@section('title')
    Dashboard
@stop
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="active"><a>Dashboard</a></li>
    </ol>
@stop

@section('content')
<div class="pcoded-content">
    <!-- Page-header start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="page-header-title">
                        <h5>Dashboard</h5>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{env('ADMIN_URL')}}/salon/dashboard"> <i class="fa fa-home"></i> </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="pcoded-inner-content">
        <!-- Main-body start -->
        <div class="main-body">
            <div class="page-wrapper">
                <!-- Page-body start -->
                <div class="page-body">
                    <div class="row">
                        <!-- task, page, download counter  start -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card">
                                <div class="card-block dashboxblock">
                                <a href="{{env('ADMIN_URL')}}/salon/services"> 
                                    <div class="row align-items-center">
                                        <div class="col-8">
                                            <h4 class="text-c-peach">{{$services}}</h4>
                                            <h6 class="text-muted m-b-0">Services</h6>
                                        </div>
                                        <div class="col-4 text-right">
                                        <i class="fas fa-spa"></i>
                                        </div>
                                    </div>
                                </a>
                                </div>

                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card">
                                <div class="card-block dashboxblock">
                                <a href="{{env('ADMIN_URL')}}/salon/staffs"> 
                                    <div class="row align-items-center">
                                        <div class="col-8">
                                            <h4 class="text-c-peach">{{$staffs}}</h4>
                                            <h6 class="text-muted m-b-0">Staffs</h6>
                                        </div>
                                        <div class="col-4 text-right">
                                        <i class="fas fa-users"></i>
                                        </div>
                                    </div>
                                </a>
                                </div>
                            
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card">
                                <div class="card-block dashboxblock">
                                <a href="{{env('ADMIN_URL')}}/salon/booking"> 
                                    <div class="row align-items-center">
                                        <div class="col-8">
                                            <h4 class="text-c-peach">{{$booking}}</h4>
                                            <h6 class="text-muted m-b-0">Booking</h6>
                                        </div>
                                        <div class="col-4 text-right">
                                        <i class="fas fa-calendar-check"></i>
                                        </div>
                                    </div>
                                </a>  
                                </div>
                           
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card">
                                <div class="card-block dashboxblock">
                                <a href="{{env('ADMIN_URL')}}/salon/reviews"> 
                                    <div class="row align-items-center">
                                        <div class="col-8">
                                            <h4 class="text-c-peach">{{$reviews}}</h4>
                                            <h6 class="text-muted m-b-0">Reviews</h6>
                                        </div>
                                        <div class="col-4 text-right">
                                            <i class="fa fa-star"></i>
                                        </div>
                                    </div>
                                </a>
                                </div>

                            </div>
                        </div>

                        <!--  acttivity and feed end -->
                    </div>
                </div>
                <!-- Page-body end -->
            </div>
            <!-- <div id="styleSelector"> </div> -->
        </div>
    </div>
</div>
@stop