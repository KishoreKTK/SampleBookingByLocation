@extends('layouts.master')

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
                        <h5>Block Booking</h5>
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
                        <li class="breadcrumb-item"><a>Block Bookings</a>
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
                        <div class="card-header d-flex flex-row justify-content-between">
                            <h5>Block Booking</h5>
                            <a class="btn btn-primary float-right" href="{{ url('mdadmin/bookingslots/add') }}">Add New Slots</a>
                        </div>
                        <div class="card-block table-border-style">
                            <div class="row">
                                <div class="col-lg-12 col-xl-12">
                                    <p>Some Text for first row col</p>
                                </div>
                            </div>
                            {{-- <div class="row">
                                <div class="col-lg-12 col-xl-12">
                                    <p>Some Texts for Second Row Col</p>
                                </div>
                            </div> --}}
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


@stop
