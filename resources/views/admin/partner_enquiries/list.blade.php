@extends('layouts.master')

@section('title')
Dashboard
@stop
@section('content')
<div class="pcoded-content">
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="page-header-title">
                        <h5>Partner Enquiries</h5>
                        <!-- <p class="m-b-0">Lorem Ipsum is simply dummy text of the printing</p> -->
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{env('ADMIN_URL')}}/dashboard"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a>Partner Enquiries</a>
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
                    <div class="card">
                         <div class="card-header">
                            <h5>Partner Enquiries</h5>
                        </div>
                        <!-- Email-card start -->
                        <div class="card-block table-border-style">
                            <div class="row">

                                <div class="col-lg-12 col-xl-12">
                                    <div class="mail-box-head row ">
                                        <div class="col-md-6">
                                            {!! Form::open(["url"=>env('ADMIN_URL')."/partner_enquiries","method"=>"get",
                                            "class"=>"form-material"]) !!}

                                            <div class="material-group searchgroup">
                                                <div class="form-group form-default">
                                                    <!-- <input type="text" name="footer-email" class="form-control" required=""> -->
                                                    {!!
                                                    Form::text("keyword",$keyword,["class"=>"form-control",'required' =>
                                                    'required']) !!}

                                                    <span class="form-bar"></span>
                                                    <label class="float-label">Search</label>
                                                </div>
                                                {{ Form::button('<i class="fas fa-search"></i>', ['type' => 'submit', 'class' => 'btn btn-primary'] )  }}
                                                <a href="{{ env('ADMIN_URL') }}/partner_enquiries" class='btn btn-primary'><i
                                                        class="fas fa-times"></i></a>
                                                <!-- <div class="material-addone">
                      <i class="icofont icofont-search"></i>
                    </div> -->
                                            </div>
                                            {!! Form::close() !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="table-responsive">
                                    @if(isset($enquiries)&& count($enquiries)>0)

                                    <table class="table table-sm table-hover table-framed">
                                        <thead>
                                            <tr>
                                                <th width="50">Company</th>
                                                <th>From</th>
                                                <th>Phone</th>
                                                <th>Email</th>
                                                <th>Message</th>
                                                <th>Date</th>
                                                <th width="30"></th>
                                            </tr>
                                        </thead>


                                        <tbody>
                                            @foreach($enquiries as $contact)
                                            <tr>
                                               
                                                <td>{{$contact->company}}</td>
                                                <td>{{$contact->name}}</td>
                                                <td>{{$contact->phone}}</td>
                                                <td>{{$contact->email}}</td>
                                                <td>{{$contact->message}}</td>

                                                <td>{{$contact->created_at->format('d-m-Y g:i A')}}</td>
                                                
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    @else
                                    <table>

                                    <tr class="unread">
                                        <td>No records found</td>

                                    </tr>
                                    </table>
                                        @endif
                                        <center>
                                            {!!
                                            $enquiries->appends(Illuminate\Support\Facades\Request::except('page'))->links()
                                            !!}
                                        </center>
                                </div>
                                <!-- Right-side section end -->
                            </div>
                        </div>
                        <!-- Email-card end -->
                    </div>
                </div>
                <!-- Page-body start -->
        </div>
        <!-- Main-body end -->

    </div>
</div>
</div>

@stop