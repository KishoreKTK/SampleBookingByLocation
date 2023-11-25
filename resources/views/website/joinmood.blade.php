@extends('website.home_layout')
@section('home_page_content')
<section class="top-padding">

</section>
<section class="analysis_area_two" id="home">
    <div class="container">
        <div class="row mt-6">
            <div class="col-12">
                <div class="main-contents wow fadeInUp">
                    <div class="title-four title-six text-center">
                        <h1>Join Mood</h1>
                        <div class="br"></div>
                    </div>
                    <div class="text-center mb-5">
                        <p>WE WOULD LOVE TO HAVE YOU AS PART OF THE MOOD FAMILY!</p>
                    </div>
                </div>
                <div class="row justify-content-center wow fadeInUp">
                    <div class="col-lg-10">
                        @if(Session::has('success'))
                        <div class="alert alert-success" role="alert">
                            <!-- <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button> -->
                            <h6> {{ Session::get('success')}}</h6>
                        </div>
                        @endif
                        @if(Session::has('error'))
                        <div class="alert alert-success" role="alert">
                            <!-- <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button> -->
                            <h6> {{ Session::get('error')}}</h6>
                        </div>
                        @endif
                    </div>
                </div>

                <form action="{{ url('JoinMoodPost') }}" method="POST" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    {{-- <input type="hidden" name="_csrf" value="{{  }}"> --}}
                    <div class="row justify-content-center wow fadeInUp">
                        <div class="col-lg-5">
                            <div class="form-group">
                                <label for="inputEmail4">Full Name</label>
                                <input name="user_name" type="text" class="form-control" value="" required>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="form-group">
                                <label for="inputEmail4">Your role</label>
                                <input name="user_role" type="text" class="form-control" value="" required>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="form-group">
                                <label for="inputEmail4">Business Name</label>
                                <input name="business_name" type="text" class="form-control" required value="">
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="form-group">
                                <label for="inputEmail4">Instagram Account</label>
                                <input name="insta_acc_link" type="text" class="form-control" value="">
                                <small id="passwordHelpBlock" class="form-text text-muted">
                                    Enter url (Eg. https://www.instagram.com/name)
                                </small>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="inputEmail4">Email</label>
                                <input name="email" type="text" class="form-control" required value="">
                            </div>
                        </div>

                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="inputEmail4">Phone</label>
                                <div class="clearfix"></div>
                                <input name="phone_number" type="tel" class="form-control" required id="telephone"
                                    value="">
                            </div>

                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="inputEmail4">Emirates of Operation</label>
                                <select class="custom-select" name="emirates" id="" required>
                                    <option selected disabled value=""></option>
                                    <option>Abu Dhabi</option>
                                    <option>Dubai</option>
                                    <option>Sharjah</option>
                                    <option>Ajman</option>
                                    <option>Umm Al Quwain</option>
                                    <option>Ras Al Khaimah</option>
                                    <option>Fujairah</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-10">
                            <div class="form-group">
                                <div class="mb-3">
                                    <label for="inputEmail4" class="mb-0">Select your primary two categories (max. of
                                        2)</label>
                                    <div class="clearfix"></div>
                                    <small class="form-text text-muted">*Please note that all your home services will be
                                        provided, this requirement is just for our purposes</small>
                                </div>

                                @foreach ($category_list as $key=>$cat)
                                <div class="custom-control custom-checkbox custom-control-inline category-checkbox">
                                    <input type="checkbox" name="categories[]" class="custom-control-input"
                                        id="customCheck{{ $key+1 }}" value="{{ $cat->id }}">
                                    <label class="custom-control-label"
                                        for="customCheck{{ $key+1 }}">{{ $cat->category }}</label>
                                </div>
                                @endforeach

                                {{--
                        <div class="custom-control custom-checkbox custom-control-inline category-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck2">
                            <label class="custom-control-label" for="customCheck2">Massage</label>
                        </div>
                        <div class="custom-control custom-checkbox custom-control-inline category-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck3">
                            <label class="custom-control-label" for="customCheck3">Hair </label>
                        </div>
                        <div class="custom-control custom-checkbox custom-control-inline category-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck4">
                            <label class="custom-control-label" for="customCheck4">Lashes</label>
                        </div>
                        <div class="custom-control custom-checkbox custom-control-inline category-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck5">
                            <label class="custom-control-label" for="customCheck5">Brows</label>
                        </div>
                        <div class="custom-control custom-checkbox custom-control-inline category-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck6">
                            <label class="custom-control-label" for="customCheck6">Men’s Grooming</label>
                        </div>
                        <div class="custom-control custom-checkbox custom-control-inline category-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck7">
                            <label class="custom-control-label" for="customCheck7">Clinic (e.g. IV drips)</label>
                        </div>
                        <div class="custom-control custom-checkbox custom-control-inline category-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck8">
                            <label class="custom-control-label" for="customCheck8">Hair Removal (e.g. waxing)</label>
                        </div>
                        <div class="custom-control custom-checkbox custom-control-inline category-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck9">
                            <label class="custom-control-label" for="customCheck9">Little Ones (e.g. children’s hair
                                cut)</label>
                        </div>
                        <div class="custom-control custom-checkbox custom-control-inline category-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck10">
                            <label class="custom-control-label" for="customCheck10">Mindfulness (e.g. Meditation, sound
                                healing etc)</label>
                        </div>
                        <div class="custom-control custom-checkbox custom-control-inline category-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck11">
                            <label class="custom-control-label" for="customCheck11">Pet’s Grooming</label>
                        </div> --}}
                            </div>
                        </div>

                        <div class="col-lg-10">
                            {{-- <div class="mb-3">
                                <label for="formFile" class="form-label">Price Sheet</label>
                                <input class="join_mood_file_class" name="pricesheet" type="file" id="formFile"  accept="application/pdf,application/vnd.ms-excel"/>
                                <small class="form-text text-muted">Supported Files are Pdf / Excel Sheet</small>
                            </div> --}}

                            <div class="form-group">
                                <label for="inputEmail4">Price Sheet</label>
                                <div class="custom-file">
                                    <input type="file" name="pricesheet" class="custom-file-input" id="customFileLang"
                                        lang="es" value="" accept="application/pdf,application/vnd.ms-excel" />
                                    <label class="custom-file-label" for="customFileLang">&nbsp</label>
                                    <small class="form-text text-muted">Supported Files are Pdf / Excel Sheet</small>
                                </div>
                            </div>

                        </div>

                        <div class="w-100"></div>
                        <div class="col-lg-2 pt-4">
                            <div class="form-group text-center">
                                <button type="submit"
                                    class="btn btn-primary btn-black btn-lg d-block mx-auto d-block">Submit</button>
                            </div>
                        </div>
                        <div class="w-100"></div>
                        <div class="col-lg-5 pt-3">
                            <div class="form-group text-center">
                                <small class="form-text text-muted">WE WILL GET BACK TO YOU SOON!</small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
</section>
@endsection
