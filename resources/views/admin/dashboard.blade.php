@extends('layouts.master')

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
                                <a href="{{env('ADMIN_URL')}}/dashboard"> <i class="fa fa-home"></i> </a>
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

                            <!-- Salons -->
                            <div class="col-xl-3 col-md-6">
                                <div class="card">
                                    <div class="card-block dashboxblock">
                                    <a href="{{env('ADMIN_URL')}}/salons">
                                        <div class="row align-items-center">
                                            <div class="col-8">
                                                <h4>{{$salons}}</h4>
                                                <h6 class="text-muted m-b-0">Salons</h6>
                                            </div>
                                            <div class="col-4 text-right">
                                            <i class="fas fa-spa"></i>
                                            </div>
                                        </div>
                                    </a>
                                    </div>
                                   <!--  <div class="card-footer bg-c-purple">
                                        <div class="row align-items-center">
                                            <div class="col-9">
                                                <p class="text-white m-b-0">% change</p>
                                            </div>
                                            <div class="col-3 text-right">
                                                <i class="fa fa-line-chart text-white f-16"></i>
                                            </div>
                                        </div>

                                    </div> -->
                                </div>
                            </div>

                            <!-- User Card -->
                            <div class="col-xl-3 col-md-6">
                                <div class="card">
                                <div class="card-block dashboxblock">
                                    <a href="{{env('ADMIN_URL')}}/users">
                                        <div class="row align-items-center">
                                            <div class="col-8">
                                                <h4>{{$users}}</h4>
                                                <h6 class="text-muted m-b-0">Users</h6>
                                            </div>
                                            <div class="col-4 text-right">
                                                <i class="fa fa-users"></i>
                                            </div>
                                        </div>
                                    </a>
                                    </div>
                                   <!--  <div class="card-footer bg-c-peach">
                                        <div class="row align-items-center">
                                            <div class="col-9">
                                                <p class="text-white m-b-0">% change</p>
                                            </div>
                                            <div class="col-3 text-right">
                                                <i class="fa fa-line-chart text-white f-16"></i>
                                            </div>
                                        </div>
                                    </div> -->
                                </div>
                            </div>

                            <!-- Booking Card -->
                            <div class="col-xl-3 col-md-6">
                                <div class="card">
                                <div class="card-block dashboxblock">
                                    <a href="{{env('ADMIN_URL')}}/booking">
                                        <div class="row align-items-center">
                                            <div class="col-8">
                                                <h4>{{$booking}}</h4>
                                                <h6 class="text-muted m-b-0">Booking</h6>
                                            </div>
                                            <div class="col-4 text-right">
                                            <i class="fas fa-calendar-check"></i>
                                            </div>
                                        </div>
                                    </a>
                                    </div>
                                   <!--  <div class="card-footer bg-c-peach">
                                        <div class="row align-items-center">
                                            <div class="col-9">
                                                <p class="text-white m-b-0">% change</p>
                                            </div>
                                            <div class="col-3 text-right">
                                                <i class="fa fa-line-chart text-white f-16"></i>
                                            </div>
                                        </div>
                                    </div> -->
                                </div>
                            </div>

                            <!-- FAQ Card -->
                            <div class="col-xl-3 col-md-6">
                                <div class="card">
                                <div class="card-block dashboxblock">
                                    <a href="{{env('ADMIN_URL')}}/faq">
                                        <div class="row align-items-center">
                                            <div class="col-8">
                                                <h4>{{$faq}}</h4>
                                                <h6 class="text-muted m-b-0">FAQ</h6>
                                            </div>
                                            <div class="col-4 text-right">
                                            <i class="fas fa-question-circle"></i>
                                            </div>
                                        </div>
                                    </a>
                                    </div>
                                   <!--  <div class="card-footer bg-c-peach">
                                        <div class="row align-items-center">
                                            <div class="col-9">
                                                <p class="text-white m-b-0">% change</p>
                                            </div>
                                            <div class="col-3 text-right">
                                                <i class="fa fa-line-chart text-white f-16"></i>
                                            </div>
                                        </div>
                                    </div> -->
                                </div>
                            </div>

                            <!-- task, page, download counter  end -->
                        </div>

                        <div class="row mt-3">
                            {{-- Recent Bookings --}}
                            <div class="col-xl-7 col-md-6 py-2">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="card-title">Recent Bookings</h5>
                                    </div>

                                    <div class="card-body">
                                        <table class="table table-sm table-hover table-framed">
                                            <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Salon</th>
                                                <th scope="col">User</th>
                                                <th scope="col">Booked Date</th>
                                                <th scope="col">Booking Price</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($recent_booking as $index=>$each)
                                                <tr>
                                                    <th scope="row"> {{$index+$recent_booking->firstItem() }}</th>
                                                    <td>{{$each->name}}</td>
                                                    <td>
                                                        <a href="{{ env('ADMIN_URL') }}/booking/details?booking_id={{$each->id}}">{{$each->first_name}} {{$each->last_name}}</a>
                                                    </td>
                                                    <td>{{$each->created_at}}</td>
                                                    <td >{{$each->amount}} AED</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-footer">
                                        <a href="{{env('ADMIN_URL')}}/booking" class=" btn btn-lg btn-primary float-right">View All Bookings</a>
                                    </div>
                                </div>
                            </div>
                            <!-- Recent Salons -->
                            <div class="col-xl-5 col-md-6 py-2">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="card-title">Recent Salons</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="list-group">
                                            @foreach ($recent_salon as $salon)
                                                <?php
                                                    if(isset($salon->image)&&$salon->image!='')
                                                    {
                                                        $salon->image   = env("IMAGE_URL")."salons/thumbnails/".$salon->image;
                                                    }
                                                    else
                                                    {
                                                        $salon->image= env("IMAGE_URL")."logo/no-picture.jpg";
                                                    }
                                                ?>
                                            <a href="{{env('ADMIN_URL')}}/salons/details?id={{$salon->id}}" class="list-group-item d-flex justify-content-between align-items-center" style=" text-decoration: none;">
                                                <strong>{{ $salon->name }}</strong>
                                                <div style="max-width: 40px;">
                                                    <img src="{{ $salon->image }}" class="img-fluid" alt="quixote">
                                                </div>
                                            </a>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <a href="{{env('ADMIN_URL')}}/salons/" class=" btn btn-lg btn-primary float-right">View All Salons</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- Page-body end -->
                </div>
                <!-- <div id="styleSelector"> </div> -->
            </div>
        </div>
    </div>
@stop
