@extends('layouts.master')

@section('title')
Dashboard
@stop
@section('content')

 <div class="pcoded-content">
    <!-- Page-header end -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="page-header-title">
                        <h5>Salon Transactions</h5>
                    </div>
                </div>
                <div class="col-md-4">
                   <ul class="breadcrumb">
                       <li class="breadcrumb-item">
                        <a href="{{env('ADMIN_URL')}}/dashboard"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a>Transactions</a>
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
                        <div class="card-header justify-content-between">
                            <h5 class="align-self-center">Salon Transactions</h5>
                            @if(isset($booking)&& count($booking)>0)
                                <a href="{{env('ADMIN_URL') }}/transactions/export" class="btn btn-primary float-right align-self-center">Export</a>
                            @endif
                        </div>

                        <div class="card-block table-border-style">
                             <div class="row">

                                <div class="col-lg-12 col-xl-12">
                                    <div class="mail-box-head row ">
                                        <div class="col-md-12">
                                            {!! Form::open(["url"=>env('ADMIN_URL')."/transactions","method"=>"get",
                                            "class"=>"form-material"]) !!}

                                           <div class="material-group searchgroup">
                                                <!-- <div class=" form-group form-default"> -->

                                                    {!!
                                                    Form::text("keyword",$keyword,["class"=>"form-control"]) !!}
                                                     @if(isset($salon_id)&& $salon_id>0)

                                                    {!! Form::select('salon_id',$salon,$salon_id,["class"=>"form-control", "placeholder"=>"Filter by salon"]) !!}
                                                    @else
                                                    {!! Form::select('salon_id',$salon,null,["class"=>"form-control", "placeholder"=>"Filter by salon"]) !!}
                                                    @endif
                                                    @if(isset($start_date)&& $start_date!='')
                                                     {!!Form::text("start_date",$start_date,["class"=>"form-control datepicker","id"=>"date", "aria-describedby"=>"emailHelp","placeholder"=>"Choose From Date","autocomplete"=>"off"]) !!}
                                                    @else
                                                     {!!Form::text("start_date",'',["class"=>"form-control datepicker","id"=>"date", "aria-describedby"=>"emailHelp","placeholder"=>"Choose From Date","autocomplete"=>"off"]) !!}

                                                    @endif
                                                     @if(isset($end_date)&& $end_date!='')

                                                     {!!Form::text("end_date",$end_date,["class"=>"form-control datepicker","id"=>"date", "aria-describedby"=>"emailHelp","placeholder"=>"Choose To Date","autocomplete"=>"off"]) !!}
                                                    @else

                                                     {!!Form::text("end_date",'',["class"=>"form-control datepicker","id"=>"date", "aria-describedby"=>"emailHelp","placeholder"=>"Choose To Date","autocomplete"=>"off"]) !!}
                                                    @endif


                                                    <label class="float-label">Search</label>
                                                <!-- </div> -->
                                                {{ Form::button('<i class="fas fa-search"></i>', ['type' => 'submit', 'class' => 'btn btn-primary'] )  }}
                                                <a href="{{ env('ADMIN_URL') }}/transactions" class='btn btn-primary'><i
                                                        class="fas fa-times"></i></a>

                                            </div>
                                            {!! Form::close() !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">

                            @if(isset($booking)&& count($booking)>0)

                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                        <th width="35">#</th>
                                            <th>Salon</th>
                                            <th>Username</th>
                                            <th>Booked On</th>
                                            <th class="text-right">Paid</th>
                                            <!-- <th class="text-right">Pending</th> -->
                                            <th class="text-right">Commission</th>
                                            {{-- <th class="text-right">VAT</th> --}}
                                            <th class="text-right">Actual Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($booking as $index=>$each)
                                        <tr>
                                            <th scope="row"> {{$index+$booking->firstItem() }}</th>
                                            <td>{{$each->name}}</td>
                                            <td>{{$each->first_name}} {{$each->last_name}}</td>
                                            <td>{{$each->created_at}}</td>
                                            <td class="text-right">{{$each->amount}} AED</td>
                                            <td class="text-right">{{$each->mood_commission}} AED</td>
                                            {{-- <td class="text-right">0 AED</td> --}}
                                            <td class="text-right">{{$each->actual_amount}} AED</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @else
                                <table>
                                    <thead>No records found</thead>
                                </table>

                                @endif
                                <center>
                                {!! $booking->appends(Illuminate\Support\Facades\Request::except('page'))->links()  !!}
                                </center>
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
</div>


@stop
