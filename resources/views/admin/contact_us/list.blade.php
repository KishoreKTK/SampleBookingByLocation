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
                        <h5>Contact Us</h5>
                        <!-- <p class="m-b-0">Lorem Ipsum is simply dummy text of the printing</p> -->
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{env('ADMIN_URL')}}/dashboard"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a>Contact Us</a>
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
                            <h5>Contact Us</h5>
                        </div>
                        <!-- Email-card start -->
                        <div class="card-block table-border-style">
                            <div class="row">

                                <div class="col-lg-12 col-xl-12">
                                    <div class="mail-box-head row ">
                                        <div class="col-md-6">
                                            {!! Form::open(["url"=>env('ADMIN_URL')."/contact_us","method"=>"get",
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
                                                <a href="{{ env('ADMIN_URL') }}/contact_us" class='btn btn-primary'><i
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
                                    @if(isset($contact_us)&& count($contact_us)>0)

                                    <table class="table table-sm table-hover table-framed">
                                        <thead>
                                            <tr>
                                                <th width="50">Status</th>
                                                <th>From</th>
                                                <th>Subject</th>
                                                <th>Date</th>
                                                <th width="30"></th>
                                            </tr>
                                        </thead>


                                        <tbody>
                                            @foreach($contact_us as $contact)
                                            <tr>
                                                <td class="text-center">


                                                    @if($contact->read==0)
                                                    <i class="icofont icofont-star text-danger"
                                                        data-toggle="tooltip" data-placement="top"
                                                        title="Not Read"></i>
                                                    @else
                                                    <i class="icofont icofont-star text-lightgray"
                                                        data-toggle="tooltip" data-placement="top"
                                                        title="Read"></i>
                                                    @endif

                                                </td>
                                                <td><a
                                                        href="{{env('ADMIN_URL')}}/contact_us/details?id={{$contact->id}}">{{$contact->name}}</a>
                                                </td>
                                                <td><a
                                                        href="{{env('ADMIN_URL')}}/contact_us/details?id={{$contact->id}}">{{$contact->subject}}</a>
                                                </td>

                                                <td>{{$contact->created_at->format('d-m-Y g:i A')}}
                                                </td>
                                                <td><a
                                                        href="{{env('ADMIN_URL')}}/contact_us/details?id={{$contact->id}}"><i
                                                            class="fa fa-eye"></i></a></td>
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
                                            $contact_us->appends(Illuminate\Support\Facades\Request::except('page'))->links()
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