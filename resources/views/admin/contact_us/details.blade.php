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
                        <li class="breadcrumb-item"><a href="{{env('ADMIN_URL')}}/contact_us">Contact Us</a>
                        <li class="breadcrumb-item"><a>Details</a>
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
                            <h5>{{$contact_us->subject}}</h5>
                            <span class="f-right">
                                {{$contact_us->created_at->format('d-m-Y g:i A')}}</span>
                        </div>
                        <div class="card-block">
                            @include('includes.msg')
                            <div class="form-group">
                                <label for="">From: {{$contact_us->name}}</label>
                                <br>
                                <label for="">Email: {{$contact_us->email}}</label>
                                <br>
                                <label for="">Subject: {{$contact_us->subject}}</label>
                            </div>
                            <div class="form-group">
                                <label for="">Description:</label>
                                <p class="italic">" {{$contact_us->description}} "</p>

                            </div>

                            {!!
                            Form::open(["url"=>env('ADMIN_URL')."/contact_us/reply","id"=>"main","method"=>"post"])
                            !!}
                            @csrf
                            <div class="form-group">
                                <label for="">Reply Your Thoughts:</label>
                                @if($contact_us->reply=='')

                                {!!
                                Form::textarea("reply",'',["id"=>"exampleTextarea-1","class"=>"form-control",'required'
                                => 'required']) !!}
                                @else
                                {!!
                                Form::textarea("reply",$contact_us->reply,["id"=>"exampleTextarea-1","class"=>"form-control",'required'
                                => 'required']) !!}

                                @endif
                                <!-- <textarea class="form-control" id="exampleTextarea-1" required=""></textarea> -->

                            </div>
                            {!! Form::hidden('id',$id) !!}

                            {!! Form::submit('Reply',["class"=>"btn btn-primary
                            m-b-0"]) !!}
                            {!! Form::close() !!}

                        </div>
                    </div>
                </div>
                <!-- Page-body end -->
            </div>
        </div>
        <!-- Main-body end -->

    </div>
</div>
</div>

@stop