<nav class="pcoded-navbar">
    <div class="sidebar_toggle">
        <a href="#">
            <i class="icon-close icons"></i>
        </a>
    </div>
    <div class="pcoded-inner-navbar main-menu">
        <div class="">
            <!-- <div class="main-menu-header"> -->
              <!-- <img class="img-80 img-radius" src="{{env('ADMIN_URL')}}/public/assets/files/assets/images/avatar-4.jpg" alt="User-Profile-Image">
              <div class="user-details">
                  <span id="more-details">{{Auth::user()->name}}<i class="fa fa-caret-down"></i></span>
              </div> -->
            <!-- </div> -->
            <div class="main-menu-header">
                <a href="{{env('ADMIN_URL')}}/dashboard">
                    <img class="img-fluid" src="{{ asset('img/logo/mood-white-logo.png') }}" alt="">
                </a>
            </div>

            <div class="main-menu-content">
                <ul>
                    <li class="more-details">
                        <a href="{{env('ADMIN_URL')}}/change_password"><i class="ti-settings"></i>Change Password</a>
                        <a href="{{env('ADMIN_URL')}}/logout"><i class="ti-layout-sidebar-left"></i>Logout</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="p-15 p-b-0">
        </div>

        <!-- <div class="pcoded-navigation-label"><a href="{{env('ADMIN_URL')}}/dashboard">Dashboard</a></div> -->
        <ul class="pcoded-item pcoded-left-item">
            <li class="@if(isset($activePage)&&$activePage=='Dashboard') active @endif">
                <a href="{{env('ADMIN_URL')}}/dashboard" class="waves-effect waves-dark">
                    <span class="pcoded-micon"><i class="fas fa-columns"></i></span>
                    <span class="pcoded-mtext">Dashboard</span>
                </a>
            </li>
            <?php
                $roles              =   Session::get('roles');
                $booking            =   Session::get('booking');
                $reviews            =   Session::get('reviews');
                $transactions       =   Session::get('transactions');
                $contact            =   Session::get('contact');
                $approvals          =   Session::get('approvals');
            ?>

             @if (in_array("Booking", $roles))
                <li class="@if(isset($activePage)&&$activePage=='Booking') active @endif">
                    @if(isset($booking)&& $booking>0)
                        <a href="{{env('ADMIN_URL')}}/booking" class="waves-effect waves-dark"><span class="dot"></span>
                            <span class="pcoded-micon"><i class="fas fa-calendar-check"></i></span>
                            <span class="pcoded-mtext">Bookings</span>
                        </a>
                    @else
                        <a href="{{env('ADMIN_URL')}}/booking" class="waves-effect waves-dark">
                            <span class="pcoded-micon"><i class="fas fa-calendar-check"></i></span>
                            <span class="pcoded-mtext">Bookings</span>
                        </a>
                    @endif
                </li>
            @endif

            {{-- @if (in_array("Block Slot", $roles))
                <li class="@if(isset($activePage)&&$activePage=='Block Slot') active @endif">
                    <a href="{{env('ADMIN_URL')}}/bookingslots/list" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="fas fa-calendar-check"></i></span>
                        <span class="pcoded-mtext">Blocked Slots</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                </li>
            @endif --}}

            @if (in_array("Salons", $roles))
                <li class="pcoded-hasmenu  @if(isset($activePage)&&$activePage=='Salons') active @endif">
                    @if(isset($approvals)&& $approvals>0)
                    <a class="waves-effect waves-dark"> <span class="dot"></span>
                        <span class="pcoded-micon"><i class="fas fa-spa"></i></span>
                        <span class="pcoded-mtext">Salons</span>

                    </a>
                    @else
                     <a class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="fas fa-spa"></i></span>
                        <span class="pcoded-mtext">Salons</span>

                    </a>
                    @endif

                    <ul class="pcoded-submenu">
                        <li class="active">
                            <a href="{{env('ADMIN_URL')}}/salons/add" class="waves-effect waves-dark">

                                <span class="pcoded-mtext">Add Salons</span>

                            </a>
                        </li>
                        <li class="active">
                            <a href="{{env('ADMIN_URL')}}/salons/" class="waves-effect waves-dark">

                                <span class="pcoded-mtext">Salons List</span>

                            </a>
                        </li>
                        @if (in_array("Approvals", $roles))
                            @if(isset($approvals)&& $approvals>0)
                                <li class="active">
                                    <a href="{{env('ADMIN_URL')}}/approvals/" class="waves-effect waves-dark">
                                        <span class="dot"></span>
                                        <span class="pcoded-mtext">Approvals</span>
                                    </a>
                                </li>
                            @else
                                <li class="active">
                                    <a href="{{env('ADMIN_URL')}}/approvals/" class="waves-effect waves-dark">
                                        <span class="pcoded-mtext">Approvals</span>
                                    </a>
                                </li>
                            @endif

                            <!--<li class="@if(isset($activePage)&&$activePage=='Approvals') active @endif">
                                <a href="{{env('ADMIN_URL')}}/approvals" class="waves-effect waves-dark">
                                    <span class="pcoded-micon"><i class="fas fa-bell"></i></span>
                                    <span class="pcoded-mtext">Approvals</span>
                                </a>
                            </li> -->
                        @endif
                    </ul>
                </li>
            @endif

            @if (in_array("Users", $roles))
                <li class="pcoded-hasmenu  @if(isset($activePage)&&$activePage=='Users') active @endif">
                    <a class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="fa fa-users"></i><b>D</b></span>
                        <span class="pcoded-mtext">Users</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li class="active">
                            <a href="{{env('ADMIN_URL')}}/users" class="waves-effect waves-dark">
                                <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                                <span class="pcoded-mtext">Registered Users</span>
                                <span class="pcoded-mcaret"></span>
                            </a>
                        </li>

                    </ul>
                </li>
                <!--    <li class="@if(isset($activePage)&&$activePage=='Users') active @endif">
                  <a href="{{env('ADMIN_URL')}}/users" class="waves-effect waves-dark">
                  <span class="pcoded-micon"><i class="fa fa-users"></i></span>
                  <span class="pcoded-mtext">Users</span>

                  </a>
                </li> -->
            @endif


            <!--  @if (in_array("Guests", $roles))
             <li class="@if(isset($activePage)&&$activePage=='Guests') active @endif">
              <a href="{{env('ADMIN_URL')}}/guests" class="waves-effect waves-dark">
              <span class="pcoded-micon"><i class="fa fa-users"></i></span>
              <span class="pcoded-mtext">Guests</span>

              </a>
            </li>
            @endif -->

            @if (in_array("Coupen", $roles))
                <li class="pcoded-hasmenu @if(isset($activePage)&&$activePage=='Coupen') active @endif">
                    <a class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="fas fa-percentage"></i></span>
                        <span class="pcoded-mtext">Promo Code</span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li class="active">
                            <a href="{{env('ADMIN_URL')}}/coupens/add" class="waves-effect waves-dark">
                                <span class="pcoded-mtext">Add Promo Code</span>
                            </a>
                        </li>
                        <li class="active">
                            <a href="{{env('ADMIN_URL')}}/coupens/" class="waves-effect waves-dark">
                                <span class="pcoded-mtext">PromoCode List</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (in_array("Categories", $roles))
                <li class="@if(isset($activePage)&&$activePage=='Categories') active @endif">
                    <a href="{{env('ADMIN_URL')}}/categories" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="fas fa-asterisk"></i></span>
                        <span class="pcoded-mtext">Categories</span>
                    </a>
                </li>
            @endif


            @if (in_array("Transactions", $roles))
                <li class="@if(isset($activePage)&&$activePage=='Transactions') active @endif">
                    @if(isset($transactions)&& $transactions>0)
                    <a href="{{env('ADMIN_URL')}}/transactions" class="waves-effect waves-dark">
                        <span class="dot"></span>
                        <span class="pcoded-micon"><i class="fas fa-money-bill-wave"></i></span>
                        <span class="pcoded-mtext">Transactions</span>
                    </a>
                    @else
                    <a href="{{env('ADMIN_URL')}}/transactions" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="fas fa-money-bill-wave"></i></span>
                        <span class="pcoded-mtext">Transactions</span>
                    </a>
                    @endif
                </li>
            @endif

            @if (in_array("Schedules", $roles))
                <li class="@if(isset($activePage)&&$activePage=='Schedules') active @endif">
                    <a href="{{env('ADMIN_URL')}}/schedules" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="fas fa-calendar-alt"></i></span>
                        <span class="pcoded-mtext">Schedules</span>
                    </a>
                </li>
            @endif

            <!--    @if (in_array("Pricing", $roles))
                <li class="@if(isset($activePage)&&$activePage=='Pricing') active @endif">
                    <a href="{{env('ADMIN_URL')}}/pricing" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="fas fa-money-bill"></i></span>
                        <span class="pcoded-mtext">Pricing</span>

                    </a>
                </li>
            @endif -->

            @if (in_array("Contact Us", $roles))
                <li class="@if(isset($activePage)&&$activePage=='Contact Us') active @endif">
                    @if(isset($contact)&& $contact>0)
                        <a href="{{env('ADMIN_URL')}}/contact_us" class="waves-effect waves-dark">
                            <span class="dot"></span>
                            <span class="pcoded-micon"><i class="fas fa-envelope"></i></span>
                            <span class="pcoded-mtext">Contact Us</span>
                        </a>
                    @else
                        <a href="{{env('ADMIN_URL')}}/contact_us" class="waves-effect waves-dark">
                            <span class="pcoded-micon"><i class="fas fa-envelope"></i></span>
                            <span class="pcoded-mtext">Contact Us</span>
                        </a>
                    @endif
                </li>
            @endif

            @if (in_array("Enquiry", $roles))
                <li class="@if(isset($activePage)&&$activePage=='Enquiry') active @endif">
                    <a href="{{env('ADMIN_URL')}}/Enquiry" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="fas fa-envelope"></i></span>
                        <span class="pcoded-mtext">Join Enquiries</span>
                    </a>
                </li>
            @endif


            @if (in_array("Partner Enquiries", $roles))
                <li class="@if(isset($activePage)&&$activePage=='Partner Enquiries') active @endif">
                    <a href="{{env('ADMIN_URL')}}/partner_enquiries" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="fas fa-envelope"></i></span>
                        <span class="pcoded-mtext">Partner Enquiries</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                </li>
            @endif

            @if (in_array("Reviews", $roles))
                <li class="pcoded-hasmenu  @if(isset($activePage)&&$activePage=='Reviews') active @endif">
                    <a class="waves-effect waves-dark">
                    <span class="pcoded-micon"><i class="fa fa-star"></i></span>
                        <span class="pcoded-mtext">Reviews</span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li class="active">
                            <a href="{{env('ADMIN_URL')}}/reviews/add_review_option" class="waves-effect waves-dark">
                                <span class="pcoded-mtext">Add Review Options</span>
                            </a>
                        </li>
                        <li class="active">
                            <a href="{{env('ADMIN_URL')}}/reviews/" class="waves-effect waves-dark">
                                <span class="pcoded-mtext">List Review</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (in_array("FAQ", $roles))
                <li class="pcoded-hasmenu  @if(isset($activePage)&&$activePage=='FAQ') active @endif">
                    <a class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="fas fa-question-circle"></i></span>
                        <span class="pcoded-mtext">FAQ</span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li class="active">
                            <a href="{{env('ADMIN_URL')}}/faq/add_category" class="waves-effect waves-dark">
                                <span class="pcoded-mtext">Add FAQ Category</span>
                            </a>
                        </li>
                        <li class="active">
                            <a href="{{env('ADMIN_URL')}}/faq/add" class="waves-effect waves-dark">
                                <span class="pcoded-mtext">Add FAQ</span>
                            </a>
                        </li>
                        <li class="active">
                            <a href="{{env('ADMIN_URL')}}/faq/" class="waves-effect waves-dark">
                                <span class="pcoded-mtext">FAQs List</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (in_array("Content", $roles))
                <li class="pcoded-hasmenu  @if(isset($activePage)&&$activePage=='Content') active @endif">
                    <a class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="fas fa-file-alt"></i></span>
                        <span class="pcoded-mtext">Content</span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li class="active">
                            <a href="{{env('ADMIN_URL')}}/content/add" class="waves-effect waves-dark">
                                <span class="pcoded-mtext">Add Content</span>
                            </a>
                        </li>
                        <li class="active">
                            <a href="{{env('ADMIN_URL')}}/content/" class="waves-effect waves-dark">
                                <span class="pcoded-mtext">Content List</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (in_array("AdminUsers", $roles))
                <li class="pcoded-hasmenu  @if(isset($activePage)&&$activePage=='AdminUsers') active @endif">
                    <a class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="fa fa-users"></i><b>D</b></span>
                        <span class="pcoded-mtext">Admin Users</span>

                    </a>
                    <ul class="pcoded-submenu">
                        <li class="active">
                            <a href="{{env('ADMIN_URL')}}/admin_users/add" class="waves-effect waves-dark">

                                <span class="pcoded-mtext">Add Admin Users</span>

                            </a>
                        </li>
                        <li class="active">
                            <a href="{{env('ADMIN_URL')}}/admin_users/" class="waves-effect waves-dark">

                                <span class="pcoded-mtext">Admin Users List</span>

                            </a>
                        </li>
                    </ul>
                </li>
            @endif
        </ul>
    </div>
</nav>
