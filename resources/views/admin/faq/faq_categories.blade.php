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
                    <h5>FAQ Category</h5>
                </div>
            </div>
            <div class="col-md-4">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{env('ADMIN_URL')}}/dashboard"> <i class="fa fa-home"></i> </a>
                    </li>
                    <li class="breadcrumb-item"><a href="{{env('ADMIN_URL')}}/faq/add_category">FAQ Categories</a></li>
                    <li class="breadcrumb-item"><a>Add</a></li>
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
            <h5>Add FAQ Categories</h5>
        </div>
        <div class="card-block">
        @include('includes.msgs')

        {!! Form::open(["url"=>env('ADMIN_URL')."/faq/add_category","id"=>"main","method"=>"post",'files'=> true]) !!}
            @csrf
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Category</label>
            <div class="col-sm-10">
                {!! Form::text("category",'',["placeholder"=>"Category","class"=>"form-control"]) !!}

                <span class="messages"></span>
            </div>
        </div>
        
        
      
        
       
        <div class="form-group row">
            <label class="col-sm-2"></label>
            <div class="col-sm-10">

                 {!! Form::submit('Submit',["class"=>"btn btn-primary m-b-0"]) !!}
            </div>
        </div>
         {!! Form::close() !!}
        </div>
    </div>
       
    </div>
    </div>

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
                    <!-- Horizontal-border table start -->
                    <div class="card">
                        <div class="card-header">
                            <h5>List Categories</h5>
                        </div>
                        <div class="card-block table-border-style">
                     @include('includes.msg')

                            <div class="table-responsive">
                                @if(isset($categories)&& count($categories)>0)
                                <table class="table table-sm table-hover table-framed">
                                    <thead>
                                        <tr>
                                            <td width="35">#</td>
                                            <td>Name</td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                       
                                    </thead>
                                    <tbody>
                                        @foreach($categories as $index=>$each)
                                        <tr>
                                            <td></td>
                                            <td>{{$each->category}}</td>
                                            
                                           
                                            
                                            <td class="text-center"  data-toggle="tooltip" data-placement="left" title="Edit"><a href="{{env('ADMIN_URL')}}/faq/categories/edit?id={{$each->id}}" class="text-muted d-block"><i class="icofont icofont-ui-edit"></i></a></td>
                                            <td class="text-center"  data-toggle="tooltip" data-placement="left" title="Delete"><a href="{{env('ADMIN_URL')}}/faq/categories/delete?id={{$each->id}}" class="text-muted d-block"><i class="icofont icofont-delete-alt"></i></a></td>
                                            
                                           

                                            
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

@stop