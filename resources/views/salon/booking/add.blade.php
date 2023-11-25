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
    <div class="pcoded-inner-content"   ng-controller="addBookingController">
        <div ng-init="salon_id = '{{$salon_id}}'"></div>

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

                            @include('includes.error')

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Choose Services</label>
                                <div class="col-sm-10">
                                    <div ng-repeat="value in services| filter:searchServices">
                                        <input type="checkbox" name="@{{value.service}}" id="@{{value.id}}"
                                            ng-model="value.checked" ng-change="selectedServices(value)">
                                        @{{value.service}}
                                    </div>

                                    <span class="messages"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card" ng-show="selected_services.length">
                        <div class="card-header">
                            <h5>Selected Services</h5>
                        </div>

                        <div class="card-block">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">


                                    <thead>
                                        <tr>
                                            <th>Service</th>
                                            <th class="text-right">Amount</th>
                                            <th width="150">Choose Staff</th>
                                            <!-- <th width="150">Choose Date</th> -->
                                            <th width="150">Choose TimeSlot</th>

                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr ng-repeat="selected in selected_services">
                                            <td class="">@{{selected.service}}</td>

                                            <td class="text-right">@{{selected.amount}} AED</td>

                                            <td class="">
                                                <div ng-if="selected.staff_name">

                                                    <button type="button" class="btn btn-primary btn-block"
                                                        data-toggle="modal" data-target="#default-Modal"
                                                        ng-click="staff(selected.id)">@{{selected.staff_name}}</button>
                                                </div>
                                                <div ng-if="!selected.staff_id && !selected_staff_name">

                                                    <button type="button" class="btn btn-primary btn-block"
                                                        data-toggle="modal" data-target="#default-Modal"
                                                        ng-click="staff(selected.id)">
                                                        Staff</button>
                                                </div>
                                                <div ng-if="staff_modal==true && selected_service==selected.id"
                                                    class="modal fade" id="default-Modal" tabindex="-1" role="dialog">
                                                    <div class="modal-dialog modal-lg" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">Staff</h4>
                                                                <button type="button" class="close btn btn-block" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h5>@{{selected.service}}</h5>
                                                                <a ng-repeat="each in staffs">
                                                                    <button type="button" class="btn btn-primary btn-block"
                                                                        ng-click="selectStaff(each)">@{{each.staff}}</button>
                                                                </a>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </td>
                                            <td class="">
                                                <div ng-if="selected.start_time && selected.end_time">
                                                    <button type="button" class="btn btn-block btn-primary" data-toggle="modal"
                                                        data-target="#large-Modal"
                                                        ng-click="timeslot(selected.id)">@{{selected.date}} : @{{selected.start_time}}
                                                        - @{{selected.end_time}}</button> </div>
                                                <div ng-if="!selected.start_time || !selected.end_time">

                                                    <button type="button" class="btn btn-block btn-primary" data-toggle="modal"
                                                        data-target="#large-Modal"
                                                        ng-click="timeslot(selected.id)">Timeslot</button> </div>
                                                <div ng-if="timeslot_modal==true && selected_service==selected.id"
                                                    class="modal fade" id="large-Modal" tabindex="-1" role="dialog">
                                                    <div class="modal-dialog modal-lg" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">TimeSlot</h4>
                                                                <button type="button" class="btn btn-block close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h5>@{{selected.service}}</h5>
                                                                <div class="form-group row">
                                                                    <label class="col-sm-2 col-form-label">Choose date</label>

                                                                    <div class="col-sm-8">
                                                                        <input type="text" ng-model="datetime" ng-change="change($index)" id="datepicker" class="form-control" placeholder="Choose date" required>
                                                                        <span class="messages"></span>
                                                                    </div>
                                                                    <div class="col-sm-2">
                                                                        <button type="button" ng-click="search_time($index,datetime,selected.id)"  class="btn btn-block btn-primary m-b-0">Search</button>

                                                                    </div>

                                                                </div>

                                                                <a ng-repeat="each in timeframes">
                                                                    <button type="button" class="btn btn-block btn-primary m-b-0"
                                                                        ng-click="selectTimeslot(each)">@{{each.start_time}}
                                                                        - @{{each.end_time}}</button>
                                                                </a>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </td>
                                        </tr>
                                    </tbody>


                                </table>


                            </div>



                            <!-- add user details -->
                            <h5>Add User Details</h5>
                            <div class="card-block">

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">First name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" ng-model="first_name" placeholder="First name" required>

                                </div>
                            </div>
                              <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Last name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" ng-model="last_name" placeholder="Last name" required>

                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" ng-model="email" placeholder="Email" required>

                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Phone</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" ng-model="phone" placeholder="Phone" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Amount</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" ng-model="amount" placeholder="Amount" required>
                                </div>
                            </div>


                             <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Address</label>
                                <div class="col-sm-10">
                                    <input type="textarea" class="form-control" ng-model="address" placeholder="Address" required>

                                </div>
                            </div>



                            <div class="form-group row">
                                <label class="col-sm-2"></label>
                                <div class="col-sm-2">
                                    <!-- <button type="submit" class="btn btn-primary m-b-0">Submit</button> -->
                                    <button type="submit" ng-click="submit(user_id,amount,first_name,last_name,email,phone,address)" class="btn btn-primary btn-block">Book</button>

                                </div>
                            </div>
                            </div>


                            <!-- add user details -->









                        </div>


                    </div>



                </div>


            </div>
            <!-- Page-body end -->
        </div>
    </div>
    <!-- Main-body end -->

</div>
</div>
<script type="text/javascript">
     $("#datepicker").datepicker(
      {
        dateFormat: "dd-mm-yy",
        minDate: 1,

    }
      );

    // });
</script>

@stop
