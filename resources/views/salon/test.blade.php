

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css" rel="stylesheet"/>

<div class="pcoded-content">
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="page-header-title">
                    <h5>Add Holidays</h5>
                </div>
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
            <h5>Add Closed dates</h5>
        </div>
        <div class="card-block">
        @include('includes.msg')

        {!! Form::open(["url"=>env('ADMIN_URL')."/salon/staffs/holidays/add","id"=>"main","method"=>"post",'files'=> true]) !!}
            @csrf
            
    
          <div class="form-group row">
            <label class="col-sm-2 col-form-label">Add closed dates</label>
            <div class="col-sm-10">
            {!! Form::text("date",'',["class"=>"form-control", "id"=>"multidatepicker", "placeholder"=>"Choose Date","autocomplete"=>"off"]) !!}

                <span class="messages"></span>
            </div>
        </div>
      
        <div class="form-group row">
            <label class="col-sm-2"></label>
            <div class="col-sm-10">
                <!-- <button type="submit" class="btn btn-primary m-b-0">Submit</button> -->

                 {!! Form::submit('Add',["class"=>"btn btn-primary m-b-0"]) !!}
            </div>
        </div>
         {!! Form::close() !!}
        </div>
        <div class="card-block">

        
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
</div>
</div>

 <script>
$(document).ready(function () {

  $('#multidatepicker').datepicker({
    format: 'dd-mm-yyyy',
    multidate: true,
    // daysOfWeekDisabled: [0, 6],
    clearBtn: true,
    todayHighlight: true,
    daysOfWeekHighlighted: [1, 2, 3, 4, 5]
  });
          
  // $('#multidatepicker').on('changeDate', function(evt) {
  //   console.log(evt.date);
  // });
});

</script>