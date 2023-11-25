@extends('layouts.master')

@section('title')
Dashboard
@stop

@section('pagecss')
<link rel="stylesheet" href="{{ asset('assets/css/jquery.timepicker.min.css') }}">
@endsection
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
                        <h5>Add Booking </h5>
                        {{-- <a href="{{url('mdadmin/transactions/export')}}"
                            class="btn btn-sm btn-primary float-right">
                            Download Report
                        </a> --}}
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{env('ADMIN_URL')}}/dashboard"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a>Block Booking</a>
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
                        <div class="card-block table-border-style">
                            <div class="row" id="CheckAvailabilityBooking">
                                <div class="col-lg-12 col-xl-12">
                                    <form id="BlockPostForm" method="post">
                                        <div class="alert alert-primary" id="errorMessagesdiv" role="alert">
                                            <h6 id="errmessage"></h6>
                                        </div>
                                        {{-- Category Field --}}
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Categories</label>
                                            <div class="col-sm-10">
                                                <div class="form-group">
                                                    <select id="select_category" class="form-control" name="categories_id">
                                                        <option value="">Please Select Category</option>
                                                        @foreach ($categorylist as $category)
                                                        <option value="{{ $category->id }}">{{ $category->category }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <span class="messages"></span>
                                            </div>
                                        </div>

                                        {{-- Salon Field --}}
                                        <div class="form-group row" id="salon_field_input">
                                            <label class="col-sm-2 col-form-label">salon</label>
                                            <div class="col-sm-10">
                                                <div class="form-group">
                                                    <select id="salonlists" class="form-control" name="salon_id">
                                                    </select>
                                                </div>
                                                <span class="messages"></span>
                                            </div>
                                        </div>

                                        {{-- Service Details Table --}}
                                        <div class="row" id="service_details_div">
                                            <div class="col-sm-12">
                                                <strong>Services</strong>
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr style="height: 2px">
                                                            <th class="col-md-1">select</th>
                                                            <th>Services</th>
                                                            <th class="col-md-1">Time</th>
                                                            <th class="col-md-1">Amount</th>
                                                            <th>No of Guests</th>
                                                            <th>Service Type</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="services_based_on_salon">

                                                    </tbody>
                                                </table>
                                                {{-- <div class="float-right mt-2" id="show_dt_time_to_button">
                                                    <button type="button" class="btn btn-primary" id="continue_to_booking">Contine to Booking</button>
                                                </div> --}}
                                            </div>
                                        </div>

                                        {{-- Service Details, Date, Time & Check Availability --}}
                                        <div class="row" id="slot_booking_date_and_time_div">
                                            <div class="col-lg-4">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <table class="mt-4 table table-bordered table-responsive">
                                                            <thead class="thead-default">
                                                                <tr>
                                                                    <th style="width:260px">Service Details</th>
                                                                    <th style="width: 140px">Total</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <th>No of Services</th>
                                                                        <td id="calculated_no_of_service"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Total Service Time</th>
                                                                        <td id="calculated_service_time"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Total Service Amount</th>
                                                                        <td id="calculated_service_amou"></td>
                                                                    </tr>
                                                                </tbody>
                                                        </table>
                                                        <input type="hidden" name="totalservicetime"    id="totalservicetime" value="">
                                                        <input type="hidden" name="totalserviceamount"  id="totalserviceamount" value="">
                                                        <input type="hidden" name="service_details" id="selectedservice_details" value="">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-4">
                                                <div class="d-flex justify-content-center">
                                                    <div class="slotformbox">
                                                        <?php
                                                            $dt 			= new DateTime();
                                                            $date		    = $dt->format('d-m-Y');
                                                        ?>
                                                        <input type="text" name="date" id="slot_dt" value="{{ $date }}" class="form-control date-flatpickr" required autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-4" style="position: relative;">
                                                <div class="d-flex flex-wrap date-box time-number  justify-content-center" id="time_intervals">
                                                </div>
                                                <p>Your Service Time is <span class="text-success" id="selected_serive_time"></span> mins</p>
                                                <p class="mt-2">
                                                    <input type="text" name="block_start_time" class="timepicker_start" value="" /> to
                                                    <input type="text" name="block_end_time" class="end_time_booking" value=""/>
                                                </p>

                                                <div class="border border-3 rounded p-3 mt-3 h-50" style="overflow:auto;" id="availability_check_div">
                                                </div>
                                                <div class="float-right mt-4" style="position: absolute;bottom: 0;right: 40px;">
                                                    <button type="button" class="btn btn-primary" id="check_slots">Check Availability</button>
                                                    {{-- <button type="button" class="btn btn-primary" id="ProceedtoBooking">Proceed to Booking</button> --}}
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="row" id="ProceedtoBookingDiv">

                                {{-- Listing Booking Details --}}
                                <div class="col-lg-5 col-xl-5">

                                    {{-- Booking Details --}}
                                    <div class="row">
                                        {{-- <p>Booking Detail</p> --}}
                                        <div class="col">
                                            <table class="mt-4 table table-bordered table-responsive">
                                                <thead>
                                                    <tr style="height: 2px">
                                                        <th>Booking</th>
                                                        <th>Details</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <th>Date</th>
                                                        <td id="selected_booking_dt"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Total Service Time</th>
                                                        <td id="selected_bookign_time"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Total Service Amount</th>
                                                        <td id="total_booking_amt"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    {{-- Service Details --}}
                                    <div class="row mt-2">
                                        {{-- <p>Service Details</p> --}}
                                        <div class="col">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr style="height: 2px">
                                                        <th >Services</th>
                                                        <th class="col-md-1">Time</th>
                                                        <th class="col-md-1">Amount</th>
                                                        <th>Guests</th>
                                                        <th>Service</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="SelectedServiceDetails">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-7 col-xl-7">
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Customer Type</label>
                                        <div class="col-sm-10">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1" Checked>
                                                <label class="form-check-label" for="inlineRadio1">Customer</label>
                                            </div>

                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">
                                                <label class="form-check-label" for="inlineRadio2">Guest</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Customers</label>
                                        <div class="col-sm-10">
                                            <select id="cutomer_to_b_booked" class="form-control" name="customer" >
                                                <option value="">Please Select Customer</option>
                                                @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->first_name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="messages"></span>
                                        </div>
                                    </div>

                                    <div class="form-group row" id="selected_cust_addr">
                                        <label class="col-sm-2 col-form-label">Address</label>
                                        <div class="col-sm-10">
                                            <select id="user_address_options" class="form-control" name="customer" >
                                            </select>
                                            <span class="messages"></span>
                                        </div>
                                    </div>

                                    <input type="hidden" name="assinged_staffs" id="assinged_staffs_to_go" value="">
                                    <button class="btn btn-primary float-right" type="button" id="AddNewBooking">Confirm Booking</button>
                                    <button class="btn btn-primary float-right mr-3" type="button" id="BacktoEditBooking">Back</button>
                                </div>
                            </div>
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

@stop

@section('pagescript')
<script type="text/javascript" src="{{ asset("assets/js/jquery.timepicker.min.js") }}"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.13.0/moment.js"></script>
{{-- <script type="text/javascript" src="{{ asset("assets/js/datepair.js") }}"></script>
<script type="text/javascript" src="{{ asset("assets/js/jquery.datepair.js") }}"></script> --}}
<script type="text/javascript" src="{{ asset('js/BlockBooking.js') }}"></script>

@endsection
