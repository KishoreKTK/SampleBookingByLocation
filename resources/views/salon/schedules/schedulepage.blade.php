@extends('layouts.master_salon')

@section('title')
Dashboard
@stop
@section('content')
<!-- <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.11.0/jquery-ui.js"></script> -->
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
                            <a href="{{env('ADMIN_URL')}}/salon/dashboard"> <i class="fa fa-home"></i> </a>
                            </li>
                            <li class="breadcrumb-item"><a>Schedule</a>
                            </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>


    <div class="pcoded-inner-content full-calender">
        <div class="main-body">
            <div class="page-wrapper">
                <div class="page-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header justify-content-between">
                                    <h5 class="align-self-center">Booking Schedules</h5>
                                    {{-- @if(isset($booking)&& count($booking)>0)
                                    <a href="{{ env('ADMIN_URL') }}/salon/transactions/export" class="btn btn-primary align-self-center">Export</a>
                                    @endif --}}
                                </div>
                                <div class="card-block">
                                     @include('includes.msg')
                                    <div class="d-flex justify-content-center">
                                        <div class="mb-4 slotformbox">
                                            <input type="text" name="date" value="{{ $date }}" class="form-control date-flatpickr" id="date", autocomplete="off" required>
                                        </div>
                                    </div>

                                    {{-- <div class="form-group row justify-content-center">
                                        <div class="col-auto">
                                            <button type="submit" class="btn btn-primary m-b-0">Submit</button>
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header justify-content-between">
                                    <h5>Bookings</h5>
                                    {{-- <div class="form-group">
                                        {!! Form::select("staff_id",$staffs,null,["class"=>"form-control form-control-sm",
                                        "placeholder"=>"Staffs","id"=>"staff",'required' => 'required']) !!}
                                    </div> --}}
                                </div>
                                <div class="card-block">
                                    <div class="row">
                                        @if(count($bookings_on_day) > 0)
                                            @foreach ($bookings_on_day as $booking)
                                            <div class="col-6">
                                                <div class="card border border-1">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between">
                                                            <h5 class="card-title">{{ $booking->bookstrttime }} - {{ $booking->bookendtime }}</h5>
                                                            @if($booking->booking_dt_status == "Upcoming")
                                                            <span class="badge btn-primary">Upcoming</span>
                                                            @endif
                                                        </div>
                                                        <hr size="2">
                                                        <div class="d-flex justify-content-between">
                                                            <strong>Staffs</strong>
                                                            <div>
                                                                @php
                                                                    $all_staffs =   $booking->allstaffs;
                                                                    $staffs     =   (explode(",",$all_staffs));
                                                                @endphp
                                                                @foreach ($staffs as $staff)
                                                                    <span class="badge badge-light">{{ \DB::table('salon_staffs')->where('id',$staff)->first()->staff }}</span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        <hr size="2">
                                                        <strong>Service Details</strong>
                                                        <table class="table table-sm table-bordered">
                                                            <tbody>
                                                                @foreach ($booking->booked_services as $services)
                                                                <tr>
                                                                    <th>{{ $services->service }}</th>
                                                                    <td>{{ $services->guest_count }} guests </td>
                                                                    <td>@if($services->service_type == 1)
                                                                        Back to Back
                                                                        @else
                                                                        At the Same Time
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                        <hr size="2">
                                                        <strong>Customer Details</strong>
                                                        <div class="border border-2 p-2">
                                                            <p><b>{{ $booking->first_name }}</b> - {{ $booking->phone }}</p>

                                                            <p>{{ $booking->address }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        @else
                                            <div class="col-12">
                                                <div class="card border border-1">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between">
                                                            <p>No Bookings found</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                               </div>
                            </div>
                        </div>
                    </div>
                    <!-- Add Contact Ends Model end-->
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('#date').change(function() {
            selecteddate = $(this).val();
            // var base_url = window.location.origin;
            window.location.href = '{{env('ADMIN_URL')}}/salon/schedules?date='+selecteddate; //causes the browser to refresh and load the requested url
        });
    });

</script>
@stop
