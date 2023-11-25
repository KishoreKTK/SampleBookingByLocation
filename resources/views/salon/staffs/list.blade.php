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
                    <h5>Staff</h5>
                </div>
            </div>
            <div class="col-md-4">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{env('ADMIN_URL')}}/salon/dashboard"> <i class="fa fa-home"></i> </a>
                    </li>
                    <li class="breadcrumb-item"><a>Staff</a>
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
            <h5>Staff</h5>
        </div>

        <div class="card-block">
             <div class="row">

                <div class="col-lg-12 col-xl-12">
                    <div class="mail-box-head row ">
                        <div class="col-md-8">
                            {!! Form::open(["url"=>env('ADMIN_URL')."/salon/staffs","method"=>"get",
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
                                <a href="{{ env('ADMIN_URL') }}/salon/staffs" class='btn btn-primary'><i
                                        class="fas fa-times"></i></a>
                                <!-- <div class="material-addone">
        <i class="icofont icofont-search"></i>
        </div> -->
                            </div>
                            {!! Form::close() !!}
                        </div>
                        <div class="col-md-3"></div>

                        <div class="col-md-1">
                         <a href="{{env('ADMIN_URL')}}/salon/staffs/add"> <button class="btn btn-primary d-block">Add </button></a>
                        </div>

                    </div>
                </div>
            </div>
            <div class="row">


        <div class="table-responsive">
                @include('includes.msg')

            <table class="table table-sm table-hover">
               @if(isset($staffs)&& count($staffs)>0)
                <thead>
                    <tr>
                        <th>Staff</th>
                        <th>Description</th>
                        <th>Holidays</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
               <tbody>
                @foreach($staffs as $each)
                <tr>
                    <td>{{$each->staff}}</td>
                    <td>{{$each->description}}</td>
                     <td width="120" class="text-center">
                       <a href="{{env('ADMIN_URL')}}/salon/staffs/holidays?staff_id={{$each->id}}" class="text-muted" data-toggle="tooltip" data-placement="left"><i class="icofont icofont-ui-edit"></i> Holidays</a></td>
                    <td width="40" class="text-center">
                       <a href="{{env('ADMIN_URL')}}/salon/staffs/edit?id={{$each->id}}" class="text-muted" data-toggle="tooltip" data-placement="left" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a></td>
                    <td width="40" class="text-center">
                       <a href="{{env('ADMIN_URL')}}/salon/staffs/delete?id={{$each->id}}" class="text-muted" data-toggle="tooltip" data-placement="left" title="" data-original-title="Delete"><i class="icofont icofont-delete-alt"></i></a>
                    </td>

                </tr>
                @endforeach
                </tbody>
                @else
                <tr><td>No records found</td></tr>
                @endif
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
