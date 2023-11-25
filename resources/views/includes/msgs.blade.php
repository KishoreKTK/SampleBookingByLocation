<center>

    @if(Session::has('msgs') AND Session::has('error') AND Session::get('error')==false)
    <div class="alert alert-primary" role="alert">
        <!-- <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button> -->
        <div class="alert-icon"><i class="fas fa-check-circle"></i></div>
        <div>
            <h6>{{ Session::get('msgs')}}</h6>
        </div>
    </div>
    @elseif(Session::has('msgs') AND Session::has('error') AND Session::get('error')==true AND
    is_array(Session::get('msgs')))

    <div class="alert alert-primary" role="alert">
        <!-- <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button> -->
        <div class="alert-icon"><i class="fas fa-times-circle"></i></div>
        @foreach(Session::get('msgs') as $index=>$value)
        <h6>{{ $value}}</h6>
        @endforeach
    </div>
    @elseif(Session::has('msgs') AND Session::has('error') AND Session::get('error')==true)

    <div class="alert alert-primary" role="alert">
        <!-- <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button> -->
        <div class="alert-icon"><i class="fas fa-times-circle"></i></div>
        <h6>{{ Session::get('msgs')}}</h6>
    </div>
    @elseif(Session::has('msgs'))
    <div class="alert alert-primary" role="alert">
        <!-- <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button> -->
        <div class="alert-icon"><i class="fas fa-exclamation-circle"></i></div>
        <h6> {{ Session::get('msgs')}}</h6>
    </div>

    @elseif(isset($msgs, $error) AND $error==false)
    <div class="alert alert-primary" role="alert">
        <!-- <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button> -->

        <div class="alert-icon"><i class="fas fa-check-circle"></i></div>
        <h6> {{$msgs}}</h6>

    </div>
    @elseif(isset($msgs, $error) AND $error==true)
    <div class="alert alert-primary" role="alert">
        <!-- <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button> -->
        <div class="alert-icon"><i class="fas fa-times-circle"></i></div>
        <h6>{{$msgs}}</h6>
    </div>
    @elseif(isset($msgs))

    <div class="alert alert-primary" role="alert">
        <!-- <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button> -->
        <div class="alert-icon"><i class="fas fa-exclamation-circle"></i></div>
        <h6>{{$msgs}}</h6>
    </div>

    @endif
</center>