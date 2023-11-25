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
                        <h4>List FAQ</h4>
                    </div>
                </div>
            	<div class="col-md-4">
                    <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{env('ADMIN_URL')}}/dashboard"> <i class="fa fa-home"></i> </a>
                    </li>
                    <li class="breadcrumb-item"><a>FAQ</a>
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
                <div class="page-body">
                     @include('includes.msg')

                    <div class="row">

                    	@if(isset($faq)&& count($faq)>0)
                    	@foreach($faq as $each)
                        <div class="col-sm-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{$each->title}}</h5>
                                    <div class="card-header-right">
                                        <ul class="list-unstyled card-option">
                                            <li>
                                                <i class="fa fa fa-wrench open-card-option"></i>
                                            </li>
                                            <li>
                                                <i class="fa fa-window-maximize full-card"></i>
                                            </li>
                                            <li>
                                                <i class="fa fa-minus minimize-card"></i>
                                            </li>
                                            <li>
                                        	<a href="{{env('ADMIN_URL')}}/faq/edit?id={{$each->id}}"><i class="icofont icofont-ui-edit"></i></a>
                                            </li>
                                            <li>
                                        	<a href="{{env('ADMIN_URL')}}/faq/delete?id={{$each->id}}"><i class="icofont icofont-delete-alt"></i></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-block">
                                    <p>
                                        {{$each->description}}
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @else
                    <div class="col-sm-6"><p>No records found</p></div>
                        @endif
                        
                    </div>
                     <center>
                        {!! $faq->appends(Illuminate\Support\Facades\Request::except('page'))->links()  !!}
                        </center>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
