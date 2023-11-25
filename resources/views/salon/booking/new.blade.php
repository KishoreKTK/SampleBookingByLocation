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
    <div class="pcoded-inner-content" >
        <div></div>

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
                                <label class="col-sm-2 col-form-label">Choose date</label>

                                <div class="col-sm-10">
                                    <input type="text" ng-model="date" class="form-control datepicker"
                                        placeholder="Choose date" required>

                                    <span class="messages"></span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Choose Services</label>
                                <div class="col-sm-10">
                                    @if(isset($services) && count($services)>0)
                                    @foreach($services as $index=>$service)
                                     <div class="custom-control custom-checkbox custom-control-inline">
                                    <input type="checkbox" class="custom-control-input" id="{{$index}}" name="services[]" value="{{$service->id}}">
                                    <label class="custom-control-label" for="{{$index}}">{{$service->service}}</label>
                                  </div>
                                    @endforeach
                                    @endif
       
                                    <span class="messages"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card" id="selected">
                        <div class="card-header">
                            <h5>Selected Services</h5>
                        </div>
<!-- 
                        <div class="card-block">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">


                                    <thead>
                                        <tr>
                                            <th>Service</th>
                                            <th class="text-right">Amount</th>
                                            <th width="150">Choose Staffs</th>
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
                                                                <h4 class="modal-title">Staffs</h4>
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
                                                        ng-click="timeslot(selected.id)">@{{selected.start_time}}
                                                        - @{{selected.end_time}}</button> </div>
                                                <div ng-if="!selected.start_time || !selected.end_time">

                                                    <button type="button" class="btn btn-block btn-primary" data-toggle="modal"
                                                        data-target="#large-Modal"
                                                        ng-click="timeslot(selected.id)">                                                        Timeslot</button> </div>
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

                                                                    <div class="col-sm-10">
                                                                        <input type="text" ng-model="date" id="datepicker" class="form-control"
                                                                            placeholder="Choose date" required>
                                                                        <span class="messages"></span>

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


                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <th>Choose User</th>
                                        <th>Amount</th>
                                        <th width="120"></th>
                                    </thead>
                                    <tbody>
                                        <td> <select class="form-control" ng-model="user_id" ng-init="user_id=''">

                                                <option ng-repeat="value in users" value="@{{value.id}}">
                                                    @{{value.first_name}}
                                                </option>
                                            </select>
                                            <span class="messages"></span></td>
                                        <td> <input type="text" class="form-control" ng-model="amount"
                                                placeholder="Choose amount" required>

                                            <span class="messages"></span></td>
                                        <td> <button type="submit" ng-click="submit(user_id,amount)"
                                                class="btn btn-primary btn-block">Book</button></td>

                                    </tbody>
                                </table>
                            </div>










        

                        </div> -->


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
var selected = new Array();
var sel_services = new Array();

 $('input[type="checkbox"]').click(function(){
    var val=$(this).val();
    if($(this).prop("checked") == true){
        sel_services.push(id:val.id);
        selected.push({id:val.id,service:val.service,amount:val.amount});
    }
    if($(this).prop("checked") == false){
        var remove =val.id;
        var index = sel_services.indexOf(val.id);
        selected_services.splice(index, 1);   
        sel_services.splice(index, 1);   
    }
    console.log(sel_services);
    console.log(selected);
});



</script>

@stop