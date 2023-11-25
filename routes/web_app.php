<?php
use Illuminate\Support\Facades\Route;
use PHPUnit\TextUI\XmlConfiguration\Group;

Route::prefix('app')->group(function()
{
	Route::group(['middleware' => ['origin']], function ()
	{
		Route::get('/test_cancel', "app\AppSalonsController@test");
		Route::get('/salon_test', "app\AppSalonsController@salon_test");

		//content apis
		Route::get('/test', "app\AppContentController@test");
		Route::get('/phpinfo', "app\AppContentController@phpinfo");

		Route::get('/privacy', "app\AppContentController@privacy");

		Route::get('/about_us', "app\AppContentController@about");
		Route::get('/terms_conditions', "app\AppContentController@terms_conditions");
        Route::get('/cancellation_policy', "app\AppContentController@cancellation_policy");
		Route::get('/privacy_policy', "app\AppContentController@privacy_policy");
		Route::get('/faq', "app\AppContentController@faq");
		Route::get('/countries', "app\AppContentController@countries");

		Route::get('/categories', "app\AppSalonsController@categories");
		Route::get('/salons', "app\AppSalonsController@salons");

		Route::get('/SaloonListing','app\AppSalonsController@SaloonListing');

		Route::get('/salon_detail', "app\AppSalonsController@salon_detail");
		Route::get('/salon_services', "app\AppSalonsController@salon_services");
		Route::get('/salon_reviews', "app\AppSalonsController@salon_reviews");
		Route::get('/salon_staffs', "app\AppSalonsController@salon_staffs");
		Route::get('/salon_time', "app\AppBookingController@time");
		Route::get('/salon_featured', "app\AppSalonsController@featured");
		Route::post('/contact_us', "app\AppContentController@contact_us");
		Route::get('/offers', "app\AppOffersController@index");
		Route::get('/offer_salons', "app\AppOffersController@offer_salons");
		Route::get('/offer_services', "app\AppOffersController@offer_services");
		Route::post('/apply_offer', "app\AppOffersController@apply_offer");

        Route::post('timeframes','app\MoodAPIBookingController@GetTimeFrames');

		Route::get('notification_list','Notification\PushNotificationController@index');

		Route::prefix('user')->group(function()
		{
			Route::post('/signup', "app\AppUserController@signup");
			Route::post('/login', "app\AppUserController@login");
			Route::post('/login_fb', "app\AppSocialLoginController@login_fb");
			Route::post('/login_gp', "app\AppSocialLoginController@login_gp");
            Route::post('/login_apple', "app\AppSocialLoginController@login_apple");
			Route::post('/forgot_password', "app\AppUserController@forgot_password");
			Route::get('/reset_password', "app\AppUserController@reset_pwd");
			Route::post('/reset_password', "app\AppUserController@reset_password");
			Route::get('/confirm_email', "app\AppUserController@confirm_email");

			// booking, reschedule, cancel apis
			Route::post('/booking', "app\AppBookingController@bookings");

			Route::post('/complete_booking', "app\AppBookingController@complete_booking");
			Route::post('/booking/update_status', "app\AppBookingController@update_status");
			Route::get('/check_booking', "app\AppBookingController@check_booking");
			Route::get('/booking/detail', "app\AppBookingController@booking_detail");
			Route::get('/reviews/review_options', "app\AppUserReviewsController@review_options");


            Route::prefix('mdbooking')->group(function(){
                Route::post('CheckAvailability','BookingController@CheckAvailability');
                Route::post('do_booking','BookingController@MakeBookingApi');
                Route::get('MyBookings','BookingController@MyBookings');
                Route::get('CancelBooking','BookingController@CancelBooking');
                Route::post("EditBooking",'BookingController@EditBooking');
                Route::get('/time_frame','BookingController@GetTimeFrame');
                Route::get('/applypromocode','AdminCoupenController@CheckPromocodeValidation');
                Route::get("BookingReviewAPI","BookingController@AddBookingReview");
                Route::get('SkipReview','BookingController@SkipReview');
                Route::get('/GetEmpheralCode','TestController@GenerateEmpheralCode');
            });

            Route::prefix('cards')->group(function(){
                Route::get('card_list','PaymentController@listCards');
                Route::get('card_detail','PaymentController@GetCardDetails');
                Route::post('save_card','PaymentController@AddCard');
                Route::get('delete_card','PaymentController@DeleteCard');

            });

            Route::prefix('location')->group(function(){
                Route::get('CheckLocation','app\MoodLocationController@CheckLocation');
                Route::post('SetLocation','app\MoodLocationController@SetLocation');
            });

            Route::post('/CheckSlotsByDate','app\BookingController@CheckBookedSlots');
            Route::post('TestBooking','app\BookingController@Booking');

            Route::post('TestBooking2','app\AppBookingController@moodbooking');

			Route::group(['middleware' => ['customer']], function ()
			{
				Route::get('/review_status', "app\AppUpdateBookingController@review_status");
				Route::post('/cancel_review', "app\AppUpdateBookingController@cancel_review");

				Route::post('/booking/save_token', "app\AppBookingController@save_token");
				Route::post('/booking/remove_token', "app\AppBookingController@remove_token");
				Route::post('/booking/default_token', "app\AppBookingController@default_token");
				Route::get('/booking/get_token', "app\AppBookingController@get_token");

				Route::post('/add_device', "app\AppUserController@add_device");
				Route::post('/hold_booking', "app\AppBookingController@hold_booking");
				Route::post('/reschedule_booking', "app\AppUpdateBookingController@reschedule_booking");
				Route::post('/cancel_booking', "app\AppUpdateBookingController@cancel_booking");
				Route::get('/upcomming_bookings', "app\AppBookingController@upcomming_bookings");
				Route::get('/past_bookings', "app\AppBookingController@past_bookings");

				Route::prefix('booking')->group(function()
				{
					Route::get('/detail', "app\AppBookingController@booking_detail");
					Route::get('/history', "app\AppBookingController@booking_history");
				});

				Route::prefix('notifications')->group(function()
				{
					Route::get('/get_notifications', "app\AppNotificationController@get_notification");
					Route::get('/read_notifications', "app\AppNotificationController@read_notification");
				});

				Route::get('/logout', "app\AppUserController@logout");
				Route::post('/salon/mark_favorite', "app\AppSalonsController@mark_favorite");
				Route::get('/favorites', "app\AppSalonsController@favorites");
				Route::get('/profile', "app\AppUserController@profile");
				Route::get('/check_suspend', "app\AppUserController@check_suspend");
				Route::post('/update_profile', "app\AppUserController@update_profile");
				Route::post('/update_img', "app\AppUserController@update_img");
				Route::post('/change_password', "app\AppUserController@change_password");


				Route::prefix('reviews')->group(function()
				{
					Route::get('/', "app\AppUserReviewsController@reviews");
					Route::get('/details', "app\AppUserReviewsController@details");
					Route::post('/add', "app\AppUserReviewsController@reviews_add");
				});


				Route::prefix('address')->group(function()
				{
					Route::get('/', "app\AppUserAddressController@index");
					Route::post('/addedit', "app\AppUserAddressController@add");
					Route::post('/edit', "app\AppUserAddressController@update");
					Route::post('/delete', "app\AppUserAddressController@delete");
					Route::get('/details', "app\AppUserAddressController@index");
                    Route::get('/defaultaddr/{id}',"app\AppUserAddressController@UpdateDefaultAddress");
				});
			});
		});
	});

    Route::get('/verifymobile',"app\AppUserAddressController@CheckMobileNum");
    Route::get('/verifyotp','app\AppUserAddressController@VerifyOtp');

});
