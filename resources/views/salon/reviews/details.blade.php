@extends('layouts.master_salon')

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
                        <h5>Reviews</h5>
                        <!-- <p class="m-b-0">Lorem Ipsum is simply dummy text of the printing</p> -->
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{env('ADMIN_URL')}}/salon/dashboard"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{env('ADMIN_URL')}}/salon/reviews">Reviews</a>
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
                            <h5>{{$review->reviews}}</h5>
                            <span class="f-right">
                                {{$review->created_at}}</span>
                        </div>
                        <div class="card-block">
                            @include('includes.msg')
                            <div class="form-group">
                                <label for="">From: {{$review->first_name}}  {{$review->last_name}}</label>
                                <br>
                                <label for="">Email: {{$review->email}}</label>
                                <br>
                                <label for="">Rating:  <?php
                        for($i=0; $i<5; ++$i){
                            echo '<i class="fa fa-star',($review->rating<=$i?'-o':''),'" aria-hidden="true" style="color:rgb(255, 204, 0)"></i>';
                        }
                        ?></label>
                            </div>
                            

                            {!!
                            Form::open(["url"=>env('ADMIN_URL')."/salon/reviews/reply","id"=>"main","method"=>"post"])
                            !!}
                            @csrf
                            <div class="form-group">
                                <label for="">Reply Your Thoughts:</label>
                                @if($review->reply=='')

                                {!!
                                Form::textarea("reply",'',["id"=>"exampleTextarea-1","class"=>"form-control",'required'
                                => 'required']) !!}
                                @else
                                {!!
                                Form::textarea("reply",$review->reply,["id"=>"exampleTextarea-1","class"=>"form-control",'required'
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