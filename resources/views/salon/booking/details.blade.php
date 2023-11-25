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
                        <li class="breadcrumb-item"><a>Details</a>
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


                    <div class="row">
                        <div class="col-md-4">

                            <div class="card">
                                <div class="card-header">
                                    <h5>Details</h5>
                                </div>
                                <div class="card-block table-border-style">
                                    <div class="table-responsive">
                                        @if(isset($booking))
                                        <table class="table table-sm table-hover">


                                            <tbody>
                                                <tr>
                                                    <th width="200">Salon</th>
                                                    <td>{{$booking->name}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Booked By</th>
                                                    <td>{{$booking->first_name}} {{$booking->last_name}}</td>
                                                </tr>

                                                <tr>
                                                    <th width="200">Phone</th>
                                                    <td>{{$booking->phone}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Booking On</th>
                                                    <td>{{$booking->created_at}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Amount Paid</th>
                                                    <td>{{$booking->amount}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Mood Commission</th>
                                                    <td>{{$booking->mood_commission}}</td>
                                                </tr>
                                                {{-- <tr>
                                                    <th>VAT Amount</th>
                                                    <td>0 AED</td>
                                                </tr> --}}
                                                <tr>
                                                    <th>Actual Amount</th>
                                                    <td>{{$booking->actual_amount}}</td>
                                                </tr>

                                                <tr>
                                                    <td colspan="2">
                                                        <h6 class="mb-4">Booking Address</h6>
                                                        <div class="alert alert-primary mt-2">
                                                            <address>
                                                                <p class="font-weight-bold">{{ $booking_address->first_name }},</p>
                                                                <p> {{ $booking_address->phone }},</p>
                                                                {{-- <p> {{ $booking_address->email }},</p> --}}
                                                                <p> {{ $booking_address->address }} </p>
                                                            </address>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>


                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Services</h5>
                                </div>
                                <div class="card-block table-border-style">
                                    <div class="table-responsive">

                                        <table class="table table-sm table-hover">
                                            <thead>
                                                <tr>
                                                    <th width="30">#</th>
                                                    <th>Service</th>
                                                    <th>Date</th>
                                                    <th>Time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(isset($services) && count($services)>0)

                                                @foreach($services as $index=>$service)
                                                <tr>
                                                    <td> {{$index+1}}</td>
                                                    <td> {{$service->service}}</td>
                                                    <td> {{$service->date}}</td>
                                                    <td>{{$service->start_time}} - {{$service->end_time}}
                                                    </td>
                                                </tr>
                                                @endforeach
                                                @endif
                                                @else
                                                <thead>No records found</thead>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Staffs</h5>
                                </div>
                                <div class="card-block table-border-style">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead>
                                                <tr>
                                                    <th width="35">#</th>
                                                    <th>Staff</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $assigned_staffs = $booking->allstaffs;
                                                    $staffs = explode( ',', $assigned_staffs);
                                                @endphp
                                                @foreach($staffs as $index=>$staff)
                                                <tr>
                                                    <td> {{$index+1}}</td>
                                                    <td>{{ \DB::table('salon_staffs')->where('id',$staff)->first()->staff}}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{--  --}}
                    </div>






                </div>
                <!-- Page-body end -->
            </div>
        </div>
        <!-- Main-body end -->

    </div>
</div>
</div>


@stop
