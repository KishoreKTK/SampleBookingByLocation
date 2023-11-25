<?php

Route::prefix('mdadmin/salon')->group(function()
{

	Route::group(['middleware' => ['origin']], function ()
	{
		Route::get('/test', "salon\SalonSchedulesController@test");



		Route::get('/booking/services', "salon\SalonNewBookingController@services");
		Route::get('/booking/search_staffs', "salon\SalonNewBookingController@search_staffs");
		Route::get('/booking/search_time', "salon\SalonNewBookingController@search_time");
		Route::post('/booking/add_booking', "salon\SalonNewBookingController@add_booking");



        Route::get('DeliveryArea','salon\SalonDeliveryAreaController@ViewDeliveryAreaPage');
        Route::post('update_deliveryarea','salon\SalonDeliveryAreaController@UpdateDeliveryArea');


		Route::get('/booking/add', "salon\SalonNewBookingController@add");
		Route::get('/block', "salon\SalonBlockSlotController@block");
		Route::get('/get_staff', "salon\SalonBlockSlotController@get_staff");
		Route::get('/get_time', "salon\SalonBlockSlotController@get_time");
		Route::get('/default_time', "salon\SalonBlockSlotController@default_time");
		Route::get('/list_block/default_time', "salon\SalonBlockSlotController@default_times");
		Route::get('/list_block/get_time', "salon\SalonBlockSlotController@get_times");
		Route::post('/block_slot', "salon\SalonBlockSlotController@block_slot");
		Route::post('/list_block/update', "salon\SalonBlockSlotController@update");


		Route::get('/', "salon\SalonLoginController@test");
		Route::get('/login', "salon\SalonLoginController@get_login")->name('login');

		Route::post('/login', "salon\SalonLoginController@login");
		Route::get('/logout', "salon\SalonLoginController@logout");
		Route::get('/forgot_password', "salon\SalonLoginController@forgot_password");
		Route::post('/forgot_password', "salon\SalonLoginController@forgot_pwd_mail");

		Route::get('/reset_password', "salon\SalonLoginController@reset_password");
		Route::post('/reset_password', "salon\SalonLoginController@reset_pwd");

		Route::group(['middleware' => ['salon']], function ()
		{
			Route::get('/change_password', "salon\SalonLoginController@change_password");
			Route::post('/change_password', "salon\SalonLoginController@change_old_password");
			Route::get('/users/details', "salon\SalonDashboardController@user");

			Route::get('/dashboard', "salon\SalonDashboardController@index");
			Route::group(['middleware' => ['salon_user']], function ()
			{
				Route::group(['prefix' => 'salon_users'], function ()
				{
					Route::get('/', "salon\SalonUserController@index");
					Route::get('/add', "salon\SalonUserController@add");
					Route::post('/add', "salon\SalonUserController@add_user");
					Route::get('/details', "salon\SalonUserController@details");
					Route::get('/edit', "salon\SalonUserController@edit");
					Route::post('/edit', "salon\SalonUserController@update");
					Route::get('/delete', "salon\SalonUserController@delete");
					Route::get('/notsuspend', "salon\SalonUserController@notsuspend");
					Route::get('/suspend', "salon\SalonUserController@suspend");
				});


				Route::prefix('offers')->group(function()
				{
					Route::get('/', "salon\SalonFullOffersController@index");
					Route::get('/edit', "salon\SalonFullOffersController@edit");
					Route::post('/edit', "salon\SalonFullOffersController@update");
					Route::get('/add', "salon\SalonFullOffersController@add");
					Route::get('/active', "salon\SalonFullOffersController@active");
					Route::get('/inactive', "salon\SalonFullOffersController@inactive");
					Route::post('/add', "salon\SalonFullOffersController@add_offer");
				});

				Route::get('/image/delete', "salon\SalonProfileController@delete_img");
				Route::prefix('profile')->group(function()
				{
					Route::get('/', "salon\SalonProfileController@profile");
					Route::get('/edit', "salon\SalonProfileController@edit");
					Route::post('/edit', "salon\SalonProfileController@update");

				});
				Route::prefix('services')->group(function()
				{
					Route::get('/', "salon\SalonServicesController@index");
					Route::get('/add', "salon\SalonServicesController@add");
					Route::post('/add', "salon\SalonServicesController@add_service");
					Route::get('/edit', "salon\SalonServicesController@edit");
					Route::post('/edit', "salon\SalonServicesController@edit_service");
					Route::get('/delete', "salon\SalonServicesController@delete");
					Route::prefix('offers')->group(function()
					{
						Route::get('/', "salon\SalonServiceOffersController@index");
						Route::get('/add', "salon\SalonServiceOffersController@add");
						Route::post('/add', "salon\SalonServiceOffersController@add_offer");
						Route::get('/edit', "salon\SalonServiceOffersController@edit");
						Route::post('/edit', "salon\SalonServiceOffersController@update");
						Route::get('/delete', "salon\SalonServiceOffersController@delete");

					});
				});

                Route::prefix('customers')->group(function()
				{
					Route::get('/', "salon\SalonCustomerController@index");
					// Route::get('/guests', "salon\SalonCustomerController@list");
                    Route::get('/details', "salon\SalonCustomerController@details");
                    // Route::get('/activity_log', "SalonCustomerController@activity_log");
                    Route::get('/suspend', "salon\SalonCustomerController@suspend");
                    Route::get('/unsuspend', "salon\SalonCustomerController@unsuspend");
				});


				Route::get('/categories', "salon\SalonServicesController@categories");
				// Route::get('/booking', "salon\SalonBookingController@booking");

				Route::prefix('list_block')->group(function()
				{
					Route::get('/', "salon\SalonBlockSlotController@list_block");
					Route::get('/delete', "salon\SalonBlockSlotController@delete");
					Route::get('/edit', "salon\SalonBlockSlotController@edit");
				});

				Route::prefix('booking')->group(function()
				{
					Route::get('/', "salon\SalonBookingController@booking");
					Route::post('/complete', "salon\SalonBookingController@complete_booking");
					Route::get('/details', "salon\SalonBookingController@details");
					Route::get('/invoice', "salon\SalonBookingController@invoice");
					Route::get('/cancel', "salon\SalonBookingController@cancel");
					// Route::get('/services', "salon\SalonNewBookingController@services");
					// Route::get('/new', "salon\SalonNewBookingController@new");
                    Route::get('/DownloadReport','salon\SalonBookingController@DownloadReport');
				});

				Route::get('/transactions', "salon\SalonBookingController@transactions");
				Route::get('/transactions/export', "salon\SalonBookingController@export");

				// Route::get('/schedules', "salon\SalonSchedulesController@index");
                // Route::get('/schedules/view', "salon\SalonSchedulesController@view");
                Route::get('/schedules', "salon\BookingScheduleController@BookingSchedules");

				Route::prefix('working_hours')->group(function()
				{
					Route::get('/', "salon\SalonTimeController@index");
					Route::get('/edit', "salon\SalonTimeController@edit");
					Route::post('/edit', "salon\SalonTimeController@edit_time");
				});

				Route::prefix('reviews')->group(function()
				{
					Route::get('/', "salon\SalonReviewsController@index");
					Route::post('/reply', "salon\SalonReviewsController@edit");
					Route::get('/details', "salon\SalonReviewsController@details");
				});

				Route::prefix('staffs')->group(function()
				{
					Route::get('/', "salon\SalonStaffsController@index");
					Route::get('/add', "salon\SalonStaffsController@add");
					Route::post('/add', "salon\SalonStaffsController@add_staff");
					Route::get('/edit', "salon\SalonStaffsController@edit");
					Route::post('/edit', "salon\SalonStaffsController@edit_staff");
					Route::get('/delete', "salon\SalonStaffsController@delete");

					Route::prefix('/holidays')->group(function()
					{
						Route::get('/', "salon\SalonHolidaysController@index");
						Route::post('/add', "salon\SalonHolidaysController@add");
						Route::get('/edit', "salon\SalonHolidaysController@edit");
						Route::post('/edit', "salon\SalonHolidaysController@update");
						Route::get('/delete', "salon\SalonHolidaysController@delete");
					});
				});

				// Route::prefix('offers')->group(function()
				// {
				// 	Route::get('/', "salon\SalonOffersController@index");
				// 	Route::get('/add', "salon\SalonOffersController@add");
				// 	Route::post('/add', "salon\SalonOffersController@add_offer");
				// 	Route::get('/edit', "salon\SalonOffersController@edit");
				// 	Route::post('/edit', "salon\SalonOffersController@update");
				// 	Route::get('/active', "salon\SalonOffersController@active");
				// 	Route::get('/inactive', "salon\SalonOffersController@inactive");
				// 	Route::get('/delete', "salon\SalonOffersController@delete");
				// });



			});
		});
	});






});



?>
