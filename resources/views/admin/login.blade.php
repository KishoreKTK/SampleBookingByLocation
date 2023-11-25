@extends('layouts.Login_layout')

@section('section_content')
<!-- Container-fluid starts -->
<div class="container">
    <div class="row">
        <div class="col-sm-12 align-self-center">
            <!-- Authentication card start -->
            {!! Form::open([
                    'url' => env('ADMIN_URL')."/login",
                    'id'=>'validate-form',
                    'class'=>"md-float-material form-material",
                    'method'=>'post'
                ]) !!}
            @csrf
            <div class="auth-box card loginbox">
                <div class="loginlogobox">
                    <a href=""> 
                        <img src="{{asset('img/logo/mood-white-logo.png') }}">
                    </a>
                </div>

                <div class="card-block pt-2">
                    <div class="row m-b-20">
                        <div class="col-md-12">
                            <h4 class="text-center">Admin Login</h4>
                        </div>
                    </div>
                    @include('includes.msg')
                    <div class="form-group form-primary">
                        <input type="text" name="email" class="form-control" placeholder="Your Email Address"
                        required="">
                    </div>
                    <div class="form-group form-primary">

                        <input type="password" name="password" class="form-control" placeholder="Password"
                        required="">

                    </div>
                    <div class="row m-t-20 text-left">
                        <div class="col-12">
                            <div class="checkbox-fade fade-in-primary d-">
                                <label>
                                    <input type="checkbox" value="">
                                    <span class="cr">
                                        <i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                                    <span class="">Remember me</span>
                                </label>
                            </div>
                            <div class="forgot-phone text-right f-right">
                                <a href="{{env('ADMIN_URL')}}/forgot_password" class="text-right f-w-600"> Forgot
                                    Password?</a>
                            </div>
                        </div>
                    </div>
                    <div class="row m-t-10">
                        <div class="col-md-12">
                            <!-- <button type="button" class="btn btn-primary btn-md btn-block waves-effect waves-light text-center m-b-20">Sign in</button> -->
                            {!! Form::submit('Login',
                                            ['class'=>'btn btn-white btn-md btn-block waves-effect text-center']) !!}
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-10 col-10">
                            <p class="text-left m-b-0"></p>
                            <p class="text-left">
                                <a href="{{url('/')}}"></a>
                            </p>
                        </div>
                        <div class="col-md-2 col-2">
                            <img src="{{asset('img/logo/logo_icon.png') }}" 
                                style="width:32px; float:right;"
                                alt="small-logo.png">
                        </div>
                    </div>
                </div>
            </div>
            <!-- </form> -->
            {!! Form::close() !!}
            <!-- end of form -->
        </div>
        <!-- end of col-sm-12 -->
    </div>
    <!-- end of row -->
</div>
<!-- end of container-fluid -->
@endsection
