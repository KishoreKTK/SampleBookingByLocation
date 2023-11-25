<nav class="pcoded-navbar">
    <div class="sidebar_toggle"><a href="#"><i class="icon-close icons"></i></a></div>
    <div class="pcoded-inner-navbar main-menu">
        <div class="">
            <div class="main-menu-header">
                <a href="{{env('ADMIN_URL')}}/salon/dashboard">
                    <!-- <img class="img-fluid" src="{{url('/')}}/public/img/logo/mood-white-logo.png" alt=""></a> -->
                    <img class="img-fluid" src="{{ asset('img/logo/mood-white-logo.png') }}" alt="">
            </div>

            <div class="main-menu-content">
                <ul>
                    <li class="more-details">
                        <a href="{{env('ADMIN_URL')}}/salon/change_password"><i class="ti-settings"></i>Change Password</a>
                        <a href="{{env('ADMIN_URL')}}/salon/logout"><i class="ti-layout-sidebar-left"></i>Logout</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="p-15 p-b-0">

        </div>
        <ul class="pcoded-item pcoded-left-item">
            <li class="@if(isset($activePage)&&$activePage=='Dashboard') active @endif">
                <a href="{{env('ADMIN_URL')}}/salon/dashboard" class="waves-effect waves-dark">
                    <span class="pcoded-micon"><i class="fas fa-columns"></i></span>
                    <span class="pcoded-mtext">Dashboard</span>

                </a>
            </li>
           <?php

                $sroles     =   Session::get('sroles');
                $sbooking   =   Session::get('sbooking');
                $sreviews   =   Session::get('sreviews');
                $schedules  =   Session::get('schedules');
                $stransactions= Session::get('stransactions');
            ?>

            @if (in_array("Booking", $sroles))
                <li class="@if(isset($activePage)&&$activePage=='Booking') active @endif">
                    @if(isset($sbooking)&& $sbooking>0)
                    <a href="{{env('ADMIN_URL')}}/salon/booking/" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="fas fa-calendar-check"></i></span><span class="dot"></span>
                        <span class="pcoded-mtext">Bookings</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                    @else
                    <a href="{{env('ADMIN_URL')}}/salon/booking/" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="fas fa-calendar-check"></i></span>
                        <span class="pcoded-mtext">Bookings</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                    @endif
                </li>
            @endif

            @if (in_array("Block Slots", $sroles))
                <li class="pcoded-hasmenu @if(isset($activePage)&&$activePage=='Block Slot') active @endif">
                    <a class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="fas fa-calendar-check"></i></span>
                        <span class="pcoded-mtext">Block Slots</span>
                        <span class="pcoded-mcaret"></span>
                    </a>

                    <ul class="pcoded-submenu">
                        <li class="active">
                            <a href="{{env('ADMIN_URL')}}/salon/block" class="waves-effect waves-dark">
                                <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                                <span class="pcoded-mtext">Block Slots</span>
                                <span class="pcoded-mcaret"></span>
                            </a>
                        </li>
                        <li class="active">
                            <a href="{{env('ADMIN_URL')}}/salon/list_block" class="waves-effect waves-dark">
                                <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                                <span class="pcoded-mtext">Blocked Slots List</span>
                                <span class="pcoded-mcaret"></span>
                            </a>
                        </li>

                        <!-- <li class="active">
                            <a href="{{env('ADMIN_URL')}}/salon/booking/" class="waves-effect waves-dark">
                                <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                                <span class="pcoded-mtext">List Booking</span>
                                <span class="pcoded-mcaret"></span>
                            </a>
                        </li> -->
                    </ul>
                </li>
            @endif

            @if (in_array("Schedules", $sroles))
                {{-- <li class="@if(isset($activePage)&&$activePage=='list_block') active @endif">
                    <a href="{{env('ADMIN_URL')}}/salon/list_block" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="fas fa-calendar-alt"></i></span>
                        <span class="pcoded-mtext">List Blocks</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                </li> --}}
                <li class="@if(isset($activePage)&&$activePage=='Schedules') active @endif">
                    @if(isset($schedules)&& $schedules>0)
                    <a href="{{env('ADMIN_URL')}}/salon/schedules" class="waves-effect waves-dark"><span class="dot"></span>
                        <span class="pcoded-micon"><i class="fas fa-calendar-alt"></i></span>
                        <span class="pcoded-mtext">Schedules</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                    @else
                    <a href="{{env('ADMIN_URL')}}/salon/schedules" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="fas fa-calendar-alt"></i></span>
                        <span class="pcoded-mtext">Schedules</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                    @endif
                </li>
            @endif


            @if (in_array("Services", $sroles))
                <li class="@if(isset($activePage)&&$activePage=='Services') active @endif">
                    <a href="{{env('ADMIN_URL')}}/salon/services/" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="fas fa-spa"></i></span>
                        <span class="pcoded-mtext">Services</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                </li>
            @endif

            <li class="@if(isset($activePage)&&$activePage=='DeliveryArea') active @endif">
                <a href="{{env('ADMIN_URL')}}/salon/DeliveryArea" class="waves-effect waves-dark">
                    <span class="pcoded-micon"><i class="fas fa-spa"></i></span>
                    <span class="pcoded-mtext">Delivery Area</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>

            {{-- @if (in_array("Offers", $sroles))
            <li class="pcoded-hasmenu  @if(isset($activePage)&&$activePage=='SalonUsers') active @endif">
                <a class="waves-effect waves-dark">
                    <span class="pcoded-micon"><i class="fas fa-percentage"></i><b>D</b></span>
                    <span class="pcoded-mtext">Offers</span>

                </a>
                <ul class="pcoded-submenu">
                    <li class="active">
                        <a href="{{env('ADMIN_URL')}}/salon/offers/add" class="waves-effect waves-dark">

                            <span class="pcoded-mtext">Add Offers</span>

                        </a>
                    </li>
                    <li class="active">
                        <a href="{{env('ADMIN_URL')}}/salon/offers/" class="waves-effect waves-dark">

                            <span class="pcoded-mtext">Offers List</span>

                        </a>
                    </li>
                </ul>
            </li>
            @endif --}}

            @if (in_array("Staff", $sroles))
            <li class="@if(isset($activePage)&&$activePage=='Staffs') active @endif">
                <a href="{{env('ADMIN_URL')}}/salon/staffs/" class="waves-effect waves-dark">
                    <span class="pcoded-micon"><i class="fa fa-users"></i></span>
                    <span class="pcoded-mtext">Staff</span>
                    <span class="pcoded-mcaret"></span>
                </a>

            </li>
            @endif

             @if (in_array("Working Hours", $sroles))
                <li class="@if(isset($activePage)&&$activePage=='Working Hours') active @endif">
                    <a href="{{env('ADMIN_URL')}}/salon/working_hours" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="fas fa-clock"></i></span>
                        <span class="pcoded-mtext">Working Hours</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                </li>
            @endif

             @if (in_array("Reviews", $sroles))
                <li class="@if(isset($activePage)&&$activePage=='Reviews') active @endif">
                    @if(isset($sreviews)&& $sreviews>0)

                    <a href="{{env('ADMIN_URL')}}/salon/reviews" class="waves-effect waves-dark"><span class="dot"></span>
                        <span class="pcoded-micon"><i class="fa fa-star"></i></span>
                        <span class="pcoded-mtext">Reviews</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                    @else

                    <a href="{{env('ADMIN_URL')}}/salon/reviews" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="fa fa-star"></i></span>
                        <span class="pcoded-mtext">Reviews</span>
                        <span class="pcoded-mcaret"></span>
                    </a>

                    @endif

                </li>
            @endif

            {{-- @if (in_array("Categories", $sroles))
                <li class="@if(isset($activePage)&&$activePage=='Categories') active @endif">
                    <a href="{{env('ADMIN_URL')}}/salon/categories" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="fas fa-asterisk"></i></span>
                        <span class="pcoded-mtext">Categories</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                </li>
                <li class="@if(isset($activePage)&&$activePage=='Booking') active @endif">
                    <a href="{{env('ADMIN_URL')}}/salon/booking" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="fas fa-calendar-check"></i></span>
                        <span class="pcoded-mtext">Booking</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                </li>

            @endif --}}

            @if (in_array("Transactions", $sroles))
                <li class="@if(isset($activePage)&&$activePage=='Transactions') active @endif">
                    @if(isset($stransactions)&& $stransactions>0)

                    <a href="{{env('ADMIN_URL')}}/salon/transactions" class="waves-effect waves-dark"><span class="dot"></span>
                        <span class="pcoded-micon"><i class="fas fa-money-bill-wave"></i></span>
                        <span class="pcoded-mtext">Transactions</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                    @else
                    <a href="{{env('ADMIN_URL')}}/salon/transactions" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="fas fa-money-bill-wave"></i></span>
                        <span class="pcoded-mtext">Transactions</span>
                        <span class="pcoded-mcaret"></span>
                    </a>

                    @endif
                </li>
            @endif

             {{-- @if (in_array("Salon Users", $sroles))
                <li class="pcoded-hasmenu  @if(isset($activePage)&&$activePage=='SalonUsers') active @endif">
                    <a class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="fa fa-users"></i><b>D</b></span>
                        <span class="pcoded-mtext">Salon Users</span>

                    </a>
                    <ul class="pcoded-submenu">
                        <li class="active">
                            <a href="{{env('ADMIN_URL')}}/salon/salon_users/add" class="waves-effect waves-dark">

                                <span class="pcoded-mtext">Add Salon Users</span>

                            </a>
                        </li>
                        <li class="active">
                            <a href="{{env('ADMIN_URL')}}/salon/salon_users/" class="waves-effect waves-dark">

                                <span class="pcoded-mtext">Salon Users List</span>

                            </a>
                        </li>
                    </ul>
                </li>
            @endif --}}

            <li class="@if(isset($activePage)&&$activePage=='Customers') active @endif">
                <a href="{{env('ADMIN_URL')}}/salon/customers" class="waves-effect waves-dark">
                    <span class="pcoded-micon"><i class="fas fa-user"></i></span>
                    <span class="pcoded-mtext">Customers</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>




            <!-- -------------------- -->

            <!-- ----------------------- -->
        </ul>
    </div>
</nav>
