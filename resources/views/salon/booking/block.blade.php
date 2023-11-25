@extends('layouts.master_salon')

@section('title')
Dashboard
@stop
@section('content')

<script type="text/javascript"
    src="{{url('/')}}/public/assets/files/bower_components/jquery/js/jquery.min.js "></script>
<?phpif(isset($_POST) && !empty($_POST['end_time'])){
        $end_time = $_POST['end_time']; 
        $start_time = $_POST['start_time'];?>
<div class="pcoded-content">
    <!-- Page-header start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="page-header-title">
                        <h5>Block slot</h5>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{env('ADMIN_URL')}}/salon/dashboard"> <i class="fa fa-home"></i> </a>
                        </li>

                        <li class="breadcrumb-item"><a>Block</a>
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
                        
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Block Slot</h5>
                                </div>
                                <div class="card-block">
                                       
                                    <div class="mb-4">
                                       <input type="text" class="form-control date-flatpickr">
                                    </div>
                                    <hr>
                                    <div class="d-flex date-box justify-content-center">


                                        <div class="d-flex time-number">
                                            <div class="btn-group-toggle" data-toggle="buttons">
                                                <label class="btn btn-secondary active">
                                                    <input type="checkbox" name="time" id="time1" checked>
                                                    09:00
                                                </label>
                                              </div>
                                              <div class="btn-group-toggle" data-toggle="buttons">
                                                <label class="btn btn-secondary">
                                                    <input type="checkbox" name="time" id="time2"> 10:00
                                                </label>
                                              </div>
                                              <div class="btn-group-toggle" data-toggle="buttons">
                                                <label class="btn btn-secondary">
                                                    <input type="checkbox" name="time" id="time3"> 12:00
                                                </label>
                                              </div>



                                        </div>




                                    </div>
                                </div>


                            </div>


                        </div>
                    </div>

                    <!-- Add Contact Ends Model end-->
                </div>
                <!-- Page body end -->
            </div>
        </div>
    </div>
    <!-- Main-body end -->

</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('.date-carousel').carousel({
        });
    });
    $("#button").click(function () {
        var salon_id = {!! str_replace("'", "\'", json_encode($salon_id))!!
    };
    var service_id = $('#service').val();
    var staff_id = $('#staff').val();
    var date = $('#date').val();
    // console.log(service_id);
    $.get('get_time?service_id=' + service_id + '&salon_id=' + salon_id + '&date=' + date + '&staff_id=' + staff_id, function (data) {
        // console.log(data.timeframes);
        $('#start_time').empty();
        $('#end_time').empty();
        $('#timeslot').empty();

        $.each(data.timeframes, function (index, subcatObj) {


            //   $('#start_time').append('<option value ="'+ subcatObj.start_time +'">'+subcatObj.start_time+'</option>');

            // $('#end_time').append('<option value ="end_time='+ subcatObj.end_time +'">'+subcatObj.end_time+'</option>');
            $('#timeslot').append('<option value ="' + subcatObj.start_time + '-' + subcatObj.end_time + '">' + subcatObj.start_time + '-' + subcatObj.end_time + '</option>');

        });
    });
});
    // $( "#timeslot" ).click(function(index, subcatObj) {
    //     console.log(index);
    //     console.log(subcatObj);
    //  $.ajax({
    //     type: 'post',
    //     data: {'start_time' : start_time, 'end_time': end_time},
    //     success: function( data ) {
    //         console.log(data);

    //     }
    // });
    // });

    $('#timeslot').click(function () {
        var salon_id = {!! str_replace("'", "\'", json_encode($salon_id))!!
    };
    var service_id = $('#service').val();
    var staff_id = $('#staff').val();
    var date = $('#date').val();

    if (salon_id && service_id && staff_id && date) {
        $.get('get_time?service_id=' + service_id + '&salon_id=' + salon_id + '&date=' + date + '&staff_id=' + staff_id, function (data) {
            // console.log(data.timeframes);
            $('#start_time').empty();
            $('#end_time').empty();
            $('#timeslot').empty();

            $.each(data.timeframes, function (index, subcatObj) {


                //   $('#start_time').append('<option value ="'+ subcatObj.start_time +'">'+subcatObj.start_time+'</option>');

                // $('#end_time').append('<option value ="end_time='+ subcatObj.end_time +'">'+subcatObj.end_time+'</option>');
                $('#timeslot').append('<option value ="' + subcatObj.start_time + '-' + subcatObj.end_time + '">' + subcatObj.start_time + '-' + subcatObj.end_time + '</option>');

            });
        });
    }
});
    $('#service').on('change', function (e) {
        var salon_id = {!! str_replace("'", "\'", json_encode($salon_id))!!
    };
    var service_id = e.target.value;

    $.get('get_staff?service_id=' + service_id + '&salon_id=' + salon_id, function (data) {
        // console.log(data);
        $('#staff').empty();

        $.each(data, function (index, subcatObj) {

            $('#staff').append('<option value ="' + index + '">' + subcatObj + '</option>');

        });
    });
});
</script>

@stop