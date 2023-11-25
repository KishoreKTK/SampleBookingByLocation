<!DOCTYPE html>
<html lang="en">

<head>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />
   <meta http-equiv="X-UA-Compatible" content="ie=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta name="robots" content="noarchive">
   <title>Mood - Your favorite salons - One booking platform</title>
   <!-- favicon -->
   <link rel="icon" type="image/png" href="favicon.png">
   <!-- bootstrap -->
   <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css')}}">
</head>
<body>
  <div class="container mt-3 p-3">
    <div class="content">
      <div class="row">
        <div class="col-md-5">
          <div class="card">
            <div class="card-header">
                <h6 class="card-title">ADD ENV Credentials</h6>   
            </div>
            <div class="card-body">
              <form method="POST" action="{{ url('add_env_det')}}" autocomplete="off">
                @csrf
                <div class="mb-3">
                  <label for="ENV_title" class="form-label">ENV Key</label>
                  <input type="text" name="key" class="form-control" id="ENV_title"
                    placeholder="Enter ENV key" value="{{ old('key')}}" required>
                  @error('key')
                  <small class="text-danger">{{$message }}</small>
                  @endif
                </div>
                 <div class="mb-3">
                  <label for="ENV_title1" class="form-label">ENV Value</label>
                  <input type="text" name="values" class="form-control" id="ENV_title1"
                    placeholder="Enter ENV value" value="{{ old('values')}}" required>
                  @error('values')
                  <small class="text-danger">{{$message }}</small>
                  @endif
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
              </form>
            </div>
          </div>
          <div class="card">
            <div class="card-header">
                <h6 class="card-title">Download ENV Report</h6>   
            </div>
            <div class="card-body">
              <center><a href="{{ url('test/export/') }}" class="btn btn-primary">Download Report</a></center>
            </div>
          </div>
        </div>
        <div class="col-md-7">
          <div class="card">
            <div class="card-header">
                  <h6 class="card-title">ENV_list</h6>   
            </div>
            <div class="card-body">
              @if(session()->has('message'))
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    {{ session()->get('message') }}
                </div>
              @endif
              <div class="table-responsive">
                <table class="table">
                  <thead class="text-primary">
                      <th>#</th>
                      <th>key</th>
                      <th>Value</th>
                      {{--  <th class="text-right">Action</th>  --}}
                  </thead>
                  <tbody>
                    @forelse ($ENV_list as $key=>$ENV)
                    <tr>
                        <td>{{ $key+intval(1) }}</td>
                        <td>{{ $ENV->key }}</td>
                        <td>{{ $ENV->values }}</td>
                        <!-- <td class="text-right">
                          <a href="{{ url('ENV/'.$ENV->id) }}">Edit</a>
                        </td> -->
                      <!--   <td class="text-right">
                          <a href="{{ url('ENV/'.$ENV->id) }}">Delete</a>
                        </td>     -->              
                    </tr>
                    @empty
                        <tr><td colspan="3" class="text-center">No ENV_list Found</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

   <!-- preloader area start -->
   <div class="preloader-wrapper" id="preloader">
      <div class="preloader">
         <div class="sk-circle">
            <div class="sk-circle1 sk-child"></div>
            <div class="sk-circle2 sk-child"></div>
            <div class="sk-circle3 sk-child"></div>
            <div class="sk-circle4 sk-child"></div>
            <div class="sk-circle5 sk-child"></div>
            <div class="sk-circle6 sk-child"></div>
            <div class="sk-circle7 sk-child"></div>
            <div class="sk-circle8 sk-child"></div>
            <div class="sk-circle9 sk-child"></div>
            <div class="sk-circle10 sk-child"></div>
            <div class="sk-circle11 sk-child"></div>
            <div class="sk-circle12 sk-child"></div>
         </div>
      </div>
   </div>
   <!-- preloader area end -->
   <!-- back to top area start -->
   <div class="back-to-top">
      <i class="fas fa-angle-up"></i>
   </div>
   <!-- back to top area end -->
  
   <!-- jquery -->
   <script src="{{ asset('assets/js/jquery.js')}}"></script>
   <!-- popper -->
   <script src="{{ asset('assets/js/popper.min.js')}}"></script>
   <!-- bootstrap -->
   <script src="{{ asset('assets/js/bootstrap.min.js')}}"></script>
</body>

</html>
