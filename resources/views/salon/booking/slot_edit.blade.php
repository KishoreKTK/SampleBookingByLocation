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
                        <h5>Block Slots</h5>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{env('ADMIN_URL')}}/salon/dashboard"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{env('ADMIN_URL')}}/salon/list_block">Blocked Slots</a>
                        </li>
                        <li class="breadcrumb-item"><a>Edit</a>
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
                                    <h5>Edit Block Slot</h5>
                                </div>
                                <div class="card-block">
                                     @include('includes.msg')

                                    {!!
                                    Form::open(["url"=>env('ADMIN_URL')."/salon/list_block/update","id"=>"main","method"=>"post",'files'=>
                                    true]) !!}
                                    @csrf
                                      <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Staff</label>
                                        <div class="col-sm-10">
                                            {!! Form::select("staff_id",$staffs,$staff_id,["class"=>"form-control",
                                            "placeholder"=>"Choose Staff","id"=>"staff",'required' => 'required']) !!}
                                            <span class="messages"></span>
                                        </div>
                                    </div>
                                       
                                    <div class="d-flex justify-content-center">
                                    <div class="mb-4 slotformbox">
                                       <!-- <input type="text" class="form-control date-flatpickr"> -->
                                        {!!Form::text("date",$date,["class"=>"form-control date-flatpickr","id"=>"date",
                                            "aria-describedby"=>"emailHelp","placeholder"=>"$date","autocomplete"=>"off",'required' => 'required']) !!}
                                    </div>
                                    </div>
                                    <hr>

                                     <div class="d-flex date-box justify-content-center">


                                     <div>
                                        <div class="d-flex flex-wrap time-number" id="content">

                                        </div>
                                       
                                    </div>



                                    </div>

                                    <div class="form-group row justify-content-center">
                                       
                                        <div class="col-auto">
                                            <!-- <button type="submit" class="btn btn-primary m-b-0">Submit</button> -->
                                            {!! Form::hidden('slot_id',$id) !!}

                                            {!! Form::submit('Submit',["class"=>"btn btn-primary m-b-0"]) !!}
                                        </div>
                                    </div>
                                    {!! Form::close() !!}
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
        var id = {!! str_replace("'", "\'", json_encode($id))!!};
        var salon_id = {!! str_replace("'", "\'", json_encode($salon_id))!!};
        var times = {!! str_replace("'", "\'", json_encode($times))!!};
        var date = {!! str_replace("'", "\'", json_encode($date))!!};
        var starting_time = {!! str_replace("'", "\'", json_encode($start_time))!!};
        console.log(starting_time);

        // var times = {!! str_replace("'", "\'", json_encode($times))!!};

        $(".date-flatpickr").flatpickr(
        {
             inline: true,
            dateFormat: "d-m-Y",
            minDate: "today",
            defaultDate: date
           
        }
          );
    var date = $('#date').val();

        $('.date-carousel').carousel({
        });
             

             //GET DEFAULT TIME
              $.get('default_time?id='+id+'&salon_id=' + salon_id + '&date=' + date, function (data) {
                $('#new_time').empty();
                 var times=[];
                var trHTML="";

               $.each(data.times, function (index, val) {
                    if(val==starting_time)
                    {
                        let trRow="<div class='btn-group-toggle' data-toggle='buttons'><label class='btn btn-secondary active'> <input type='checkbox' name='start_time' value='"+val+"' checked>"+val+"</label></div>";
                        trHTML=trHTML+trRow;

                    }
                    else
                    {
                       let trRow="<div class='btn-group-toggle' data-toggle='buttons'><label class='btn btn-secondary'> <input type='checkbox' name='start_time' value='"+val+"' >"+val+"</label></div>";
                        trHTML=trHTML+trRow;

                    }
                

                   
                    // $("div.shop").append(val);

                });
                 $('#content').empty();
                $('#content').append(trHTML);

            });

    });


//time click
$(document).on('change', '[type=checkbox]', function() {
// code here

$('.time-number .btn-secondary').removeClass('active');
  if(this.checked) {
    var start_time = $(this).val();
    $(this).addClass('active');
}
else
{

}

});


     $('#date').change(function () {
        var date = $('#date').val();
        var salon_id = {!! str_replace("'", "\'", json_encode($salon_id))!!};

        var staff_id = $('#staff').val();
        console.log(staff_id);
        if(staff_id=='')
        {
                $.get('default_time?id='+id+'&salon_id=' + salon_id + '&date=' + date, function (data) {
                $('#new_time').empty();
                 var times=[];
                var trHTML="";

               $.each(data.times, function (index, val) {
                   let trRow="<div class='btn-group-toggle' data-toggle='buttons'><label class='btn btn-secondary'> <input type='checkbox' name='start_time' value='"+val+"'>"+val+"</label></div>";
                    trHTML=trHTML+trRow;
                    // $("div.shop").append(val);

                });
                 $('#content').empty();
                $('#content').append(trHTML);

            });
        }
        else
        {
            $.get('get_time?id='+id+'&salon_id=' + salon_id + '&date=' + date + '&staff_id=' + staff_id, function (data) {
            $('#new_time').empty();
             var times=[];
            var trHTML="";

           $.each(data.times, function (index, val) {
               let trRow="<div class='btn-group-toggle' data-toggle='buttons'><label class='btn btn-secondary'> <input type='checkbox' name='start_time' value='"+val+"' >"+val+"</label></div>";
                trHTML=trHTML+trRow;
                // $("div.shop").append(val);

            });
             $('#content').empty();
            $('#content').append(trHTML);

        });
        }
            


    });

    $('#timeslot').click(function () {
        var salon_id = {!! str_replace("'", "\'", json_encode($salon_id))!!};
   
    var staff_id = $('#staff').val();
    var date = $('#date').val();

    if (salon_id && staff_id && date) {
        $.get('get_time?id='+id+'&salon_id=' + salon_id + '&date=' + date + '&staff_id=' + staff_id, function (data) {
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



    $('#staff').on('change', function (e) {
        var salon_id = {!! str_replace("'", "\'", json_encode($salon_id))!!};
       var staff_id = $('#staff').val();
        var date = $('#date').val();
          if(staff_id=='')
        {
                $.get('default_time?id='+id+'&salon_id=' + salon_id + '&date=' + date, function (data) {
                $('#new_time').empty();
                 var times=[];
                var trHTML="";

               $.each(data.times, function (index, val) {
                   let trRow="<div class='btn-group-toggle' data-toggle='buttons'><label class='btn btn-secondary'> <input type='checkbox' name='start_time' value='"+val+"'>"+val+"</label></div>";
                    trHTML=trHTML+trRow;
                    // $("div.shop").append(val);

                });
                 $('#content').empty();
                $('#content').append(trHTML);

            });
        }
        else
        {
            $.get('get_time?id='+id+'&salon_id=' + salon_id + '&date=' + date + '&staff_id=' + staff_id, function (data) {
            $('#new_time').empty();
             var times=[];
            var trHTML="";

           $.each(data.times, function (index, val) {
               let trRow="<div class='btn-group-toggle' data-toggle='buttons'><label class='btn btn-secondary'> <input type='checkbox' name='start_time' value='"+val+"'>"+val+"</label></div>";
                trHTML=trHTML+trRow;
                // $("div.shop").append(val);

            });
             $('#content').empty();
            $('#content').append(trHTML);

        });
        }
});
</script>

@stop