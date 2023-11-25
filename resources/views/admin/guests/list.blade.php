@extends('layouts.master')

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
                    <h5>Guests List</h5>
                </div>
            </div>
            <div class="col-md-4">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{env('ADMIN_URL')}}/dashboard"> <i class="fa fa-home"></i> </a>
                    </li>
                    <li class="breadcrumb-item"><a>Guests</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
    <!-- Page-header end -->
<div class="pcoded-inner-content">
<div class="main-body">
<div class="page-wrapper">
    <!-- Page body start -->
    <div class="page-body">
    <div class="row">
    <div class="col-sm-12">

        <!-- Product list card start -->
    <div class="card">
        <div class="card-header">
            <h5>Guests List</h5>
        </div>

        <div class="card-block">
        <div class="row">

        <div class="col-lg-12 col-xl-12">
            <div class="mail-box-head row ">
                <div class="col-md-6">
                    {!! Form::open(["url"=>env('ADMIN_URL')."/guests","method"=>"get",
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
                        <a href="{{ env('ADMIN_URL') }}/guests" class='btn btn-primary'><i
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
    @include('includes.msg')

    <div class="table-responsive">

                <table class="table table-sm table-hover dt-responsive nowrap">
                    @if(isset($guests)&& count($guests)>0)
                    <thead>
                        <tr> 
                            <th width="35">#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th width="30"></th>
                            <th width="30"></th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($guests as $index=>$user)
                        <tr>
                            <td>{{$index+$guests->firstItem() }}</td>
                            <td class="pro-name">
                                <h6>{{$user->first_name}} {{$user->last_name}}</h6>
                            </td>
                            <td>{{$user->email}}</td>
                            <td>{{$user->phone}}</td>
                            <td>{{$user->address}}</td>
                            <td></td>
                            <td> <a href="{{env('ADMIN_URL')}}/guests/details?guest_id={{$user->id}}" class="m-r-15 text-muted" data-toggle="tooltip" data-placement="top" title="" data-original-title="View"><i class="fa fa-eye" aria-hidden="true"></i></a></td>

                        </tr>
                        @endforeach
                    </tbody>
                    @else
                    <tr><td>No guests found</td></tr>
                    @endif
                     <center>
                    {!! $guests->appends(Illuminate\Support\Facades\Request::except('page'))->links()  !!}
                    </center>
                </table>
     
    </div>
    </div>
    </div>
    </div>
        <!-- Product list card end -->
    </div>
    </div>

        <!-- Add Contact Ends Model end-->
    </div>
    <!-- Page body end -->
</div>
</div>
</div>
</div>

@stop