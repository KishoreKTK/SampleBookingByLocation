@extends('layouts.Login_layout')

@section('section_content')
    <!-- Container-fluid starts -->
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <!-- Authentication card start -->
                {!! Form::open(['url' =>env('ADMIN_URL')."/reset_password",'id'=>'validate-form','class'=>"md-float-material
                form-material",'method'=>'post']) !!}
                @csrf
                <!-- <form class="md-float-material form-material"> -->
                <div class="text-center">
                    <!-- <img src="{{url('/')}}/public/assets/files/assets/images/logo.png" alt="logo.png"> -->

                    <a href=""> 
                        <img src="{{asset('img/logo/mood-white-logo.png') }}">
                        <!-- <img src="{{url('/')}}/public/img/logo/mood-white-logo.png"
                        style="width:120px;" alt="logo.png"> -->
                    </a>

                </div>
                <div class="auth-box card">
                    <div class="card-block">
                        @include('includes.msg')
                        <div class="row m-b-20">
                            <div class="col-md-12">
                            <h4 class="text-center">Reset your password</h4>
                            </div>
                        </div>



                        <div class="form-group form-primary">
                            <label class="">Password</label>
                            <input type="password" name="password" class="form-control" required="">
                            <span class="form-bar"></span>

                        </div>
                        <div class="form-group form-primary">
                            <label class="">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required="">
                            <span class="form-bar"></span>

                        </div>




                        <div class="row">
                            <div class="col-md-12">
                                <!-- <button type="button" class="btn btn-primary btn-md btn-block waves-effect text-center m-b-20">Reset Password</button> -->
                                {!! Form::hidden('email',$email) !!}
                                {!! Form::hidden('token',$token) !!}
                                {!! Form::submit('Save',['class'=>'btn btn-primary btn-md btn-block waves-effect text-center']) !!}

                            </div>
                        </div>


                        <div class="row m-t-10">
                            <div class="col-md-10">
                                <a href="{{env('ADMIN_URL')}}/login" class="text-right f-w-600"> Back to
                                    Login</a>
                            </div>
                            <div class="col-md-2">
                                <a href="#"> 
                                    <img src="{{asset('img/logo/logo_icon.png') }}" 
                                    style="width:32px; float:right;"
                                    alt="small-logo.png">
                                    <!-- <img src="{{url('/')}}/public/img/logo/logo_icon.png"
                                        style="width:32px;" alt="small-logo.png"> -->
                                </a>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- </form> -->
                {!! Form::close() !!}

                <!--<div class="login-card card-block auth-body mr-auto ml-auto">-->
                <!--<form class="md-float-material form-material">-->
                <!--<div class="text-center">-->
                <!--<img src="{{url('/')}}/public/assets/files/assets/images/logo.png" alt="logo.png">-->
                <!--</div>-->
                <!--<div class="auth-box">-->
                <!---->
                <!--</div>-->
                <!--</form>-->
                <!--&lt;!&ndash; end of form &ndash;&gt;-->
                <!--</div>-->
                <!-- Authentication card end -->
            </div>
            <!-- end of col-sm-12 -->
        </div>
        <!-- end of row -->
    </div>
    <!-- end of container-fluid -->
@endsection