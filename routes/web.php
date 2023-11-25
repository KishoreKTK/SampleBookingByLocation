<?php
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|

sk_test_51JoNj6HBN2bK41VW9Xf0iKYagtQWaTSFD1U6npPqd449WN9rMoO3tGYjxx0ZmkUIv1vqKuEA69GBuKEk2yXnloV200G5COsdXn
*/
Route::view('/', 'website.home')->name('homepage');

Route::get('/joinmood','MoodController@JoinMoodForm');
Route::post('JoinMoodPost','MoodController@PostJoinMoodEnquiry');
Route::view('privacy-policy','website.privacy_policy');
Route::view('terms-and-condtions','website.terms_n_conditions');


require "web_app.php";
require "web_salon.php";


Route::prefix('mdadmin')->group(function()
{
	Route::get('/', "AdminController@test");
	Route::get('/test_email', "AdminController@test_email");

	Route::get('/login', "AdminController@get_login")->name('login');
	Route::get('/logout', "AdminController@logout")->name('logout');
	Route::post('/login', "AdminController@login");
	Route::get('/forgot_password', "AdminController@forgot_password");
	Route::post('/forgot_password', "AdminController@forgot_pwd_mail");
	Route::get('/reset_password', "AdminController@reset_password");
	Route::post('/reset_password', "AdminController@reset_pwd");

	Route::group(['middleware' => ['auth','admin']], function ()
	{
		Route::get('/change_password', "AdminController@change_password");
		Route::post('/change_password', "AdminController@change_old_password");

		Route::get('/dashboard', "DashboardController@index");

		Route::prefix('schedules')->group(function()
		{
			Route::get('/', "AdminSchedulesController@index");
		});

		Route::prefix('reviews')->group(function()
		{
			Route::get('/', "DashboardController@reviews");
			Route::get('/add_review_option', "DashboardController@add_review_option");
			Route::post('/add_review_option', "DashboardController@save_review_option");
			Route::post('/edit_review_option', "DashboardController@edit_review_option");
			Route::get('/delete', "DashboardController@delete");
		});

		Route::prefix('approvals')->group(function()
		{
			Route::get('/', "AdminApprovalsController@index");
			Route::get('/details', "AdminApprovalsController@details");
			Route::get('/approve', "AdminApprovalsController@approve");
		});

		Route::prefix('offers')->group(function()
		{
			Route::get('/', "AdminOffersController@index");
			Route::get('/add', "AdminOffersController@add");
			Route::post('/add', "AdminOffersController@add_offer");
			Route::get('/edit', "AdminOffersController@edit");
			Route::post('/edit', "AdminOffersController@update");
			Route::get('/active', "AdminOffersController@active");
			Route::get('/inactive', "AdminOffersController@inactive");
			Route::get('/delete', "AdminOffersController@delete");
		});

		Route::prefix('pricing')->group(function()
		{
			Route::get('/', "AdminPricingController@pricing");
			Route::post('/update', "AdminPricingController@update");
		});

		Route::prefix('categories')->group(function()
		{
			Route::get('/', "AdminCategoriesController@index");
			Route::post('/add', "AdminCategoriesController@add");
			Route::get('/edit', "AdminCategoriesController@edit");
			Route::post('/edit', "AdminCategoriesController@update");
			Route::get('/delete', "AdminCategoriesController@delete");
            Route::get('/statusupdate', "AdminCategoriesController@StatusUpdate");
		});

		Route::prefix('content')->group(function()
		{
			Route::get('/', "AdminContentController@index");
			Route::get('/add', "AdminContentController@add");
			Route::post('/add', "AdminContentController@add_content");
			Route::get('/edit', "AdminContentController@edit");
			Route::post('/edit', "AdminContentController@update");
			Route::get('/delete', "AdminContentController@delete");
		});


		Route::group(['prefix' => 'admin_users'], function ()
		{
			Route::get('/', "AdminUserController@index");
			Route::get('/add', "AdminUserController@add");
			Route::post('/add', "AdminUserController@add_user");
			Route::get('/details', "AdminUserController@details");
			Route::get('/edit', "AdminUserController@edit");
			Route::post('/edit', "AdminUserController@update");
			Route::get('/delete', "AdminUserController@delete");
			Route::get('/notsuspend', "AdminUserController@notsuspend");
			Route::get('/suspend', "AdminUserController@suspend");
		});

		Route::prefix('salons')->group(function()
		{
			Route::get('/', "AdminSalonsController@index")->name('salonlistpage');
			Route::get('/add', "AdminSalonsController@add");
			Route::post('/add_salon', "AdminSalonsController@add_salon");
			Route::get('/edit', "AdminSalonsController@edit");
			Route::post('/edit', "AdminSalonsController@update");
			Route::get('/active', "AdminSalonsController@active");
			Route::get('/inactive', "AdminSalonsController@inactive");
            Route::get('/delete', "AdminSalonsController@delete");
			Route::get('/details', "AdminSalonsController@details");
			Route::get('/featured', "AdminSalonsController@featured");
			Route::get('/remove_featured', "AdminSalonsController@remove_featured");
			Route::get('/image/delete', "AdminSalonsController@delete_img");
			Route::get('downloadReport','AdminSalonsController@ExcelDownloadReport');

            Route::get('deliveryarea','AdminSalonsController@ViewDeliveryAreaPage');
            Route::post('update_deliveryarea','AdminSalonsController@UpdateDeliveryArea');
			Route::prefix('working_hours')->group(function()
			{
				Route::get('/edit', "AdminSalonTimeController@edit");
				Route::post('/edit', "AdminSalonTimeController@edit_time");
			});

			Route::prefix('staffs')->group(function()
			{
				Route::get('/add', "AdminSalonStaffsController@add");
				Route::post('/add', "AdminSalonStaffsController@add_staff");
				Route::get('/edit', "AdminSalonStaffsController@edit");
				Route::post('/edit', "AdminSalonStaffsController@edit_staff");
				Route::get('/delete', "AdminSalonStaffsController@delete");
			});

			Route::prefix('services')->group(function()
			{
				Route::get('/add', "AdminSalonServicesController@add");
				Route::post('/add', "AdminSalonServicesController@add_service");
				Route::get('/edit', "AdminSalonServicesController@edit");
				Route::post('/edit', "AdminSalonServicesController@edit_service");
				Route::get('/delete', "AdminSalonServicesController@delete");

				Route::prefix('offers')->group(function()
				{
					Route::get('/', "AdminServiceOffersController@index");
					Route::get('/add', "AdminServiceOffersController@add");
					Route::post('/add', "AdminServiceOffersController@add_offer");
					Route::get('/edit', "AdminServiceOffersController@edit");
					Route::post('/edit', "AdminServiceOffersController@update");
					Route::get('/delete', "AdminServiceOffersController@delete");
				});
			});
		});

        Route::prefix('coupens')->group(function()
        {
            Route::get('/', "AdminCoupenController@index")->name('coupenlist');
            Route::get('/add', "AdminCoupenController@add");
            Route::post('/add', "AdminCoupenController@add_promocode");
            Route::get('/delete', "AdminCoupenController@deletepromocode");
        });


		Route::prefix('guests')->group(function()
		{
			Route::get('/', "AdminGuestsController@index");
			Route::get('/details', "AdminGuestsController@details");
		});

		Route::prefix('users')->group(function()
		{
			Route::get('/', "AdminCustomersController@index");
			Route::get('/details', "AdminCustomersController@details");
			Route::get('/activity_log', "AdminCustomersController@activity_log");
			Route::get('/suspend', "AdminCustomersController@suspend");
			Route::get('/unsuspend', "AdminCustomersController@unsuspend");
			Route::get('/CustomerReport',"AdminCustomersController@downloadcustomerreport");
		});

		Route::prefix('booking')->group(function()
		{
			Route::get('/', "AdminBookingController@booking");
			Route::get('/block_slot', "AdminBookingController@slots");
			Route::get('/block_slot/delete', "AdminBookingController@delete");
			Route::get('/details', "AdminBookingController@details");
			Route::get('/invoice', "AdminBookingController@invoice");
			Route::get('/cancel', "AdminBookingController@cancel");
            Route::get('/bookingreport','AdminBookingController@DownloadBookingReport');
		});

        Route::prefix('bookingslots')->group(function()
		{
            Route::get('/list',"BookingController@SlotsBookedList");
            Route::get('/add',"BookingController@CreateNewBookingSlotPage");

            Route::post('salonlistfromcat', 'BookingController@GetSalonsUnderCategory');
            Route::post('selectsalonservices','BookingController@SelectSalonServices');
            Route::post('changeservicedate','BookingController@SelctedServiceDate');

            Route::post('CheckAvailability','BookingController@CheckAvailability');
            Route::post('/GetCustomerAddress','BookingController@GetCustomerAddress');
            Route::post('MakeBooking','BookingController@MakeBooking');
            Route::get('/Create',"BookingController@SlotsBookedList");
        });

        Route::get('/Enquiry','MoodController@EnquiryList');

		Route::get('/transactions', "AdminBookingController@transactions");
		Route::get('/transactions/export', "AdminBookingController@export");

		Route::prefix('faq')->group(function()
		{
			Route::get('/', "AdminFaqController@index");
			Route::get('/details', "AdminFaqController@details");
			Route::get('/add', "AdminFaqController@add");
			Route::post('/add', "AdminFaqController@add_faq");
			Route::get('/edit', "AdminFaqController@edit");
			Route::post('/edit', "AdminFaqController@update");
			Route::get('/delete', "AdminFaqController@delete");
			Route::get('/add_category', "AdminFaqController@add_category");
			Route::post('/add_category', "AdminFaqController@add_category_post");
		});

		Route::prefix('contact_us')->group(function()
		{
			Route::get('/', "AdminContactController@contact_us");
			Route::get('/details', "AdminContactController@details");
			Route::post('/reply', "AdminContactController@reply");
		});

		Route::get('/partner_enquiries/', "AdminContactController@penquiries");
		// Route::get('/partner_enquiries/details', "AdminContactController@pdetails");
	});
});



Route::get("/get_enc_db_pass",function(){
	$password = base64_encode("moS3*1re2oqigap&9r#g");
    return $password;
});

Route::get("/testConfig",function(){
	$test = Config('mail.driver');
		return $test;
});

Route::get('/TestMail','AppSettingsController@TestMail');
// Route::get('/check_db_conn','AppSettingsController@CheckDBConnection');
Route::get('/testing_query','AppSettingsController@TestMailCredentials');
Route::get("AppSettings",'AppSettingsController@envindex');
// Route::post("add_env_det",'AppSettingsController@envstore');
Route::get('test/export/', 'AppSettingsController@export');

// Route::post("edit_env_det",'AppSettingsController@envedit');
// Route::post("edit_env_det",'AppSettingsController@envdelete');

Route::view('test_stipe_gateway','TestingPurpose.payment_gateway_test');
Route::view('test_location','TestingPurpose.location_test');


Route::view('test_location2','TestingPurpose.location_test1');
Route::view('test_location1','TestingPurpose.location_test');


Route::view('test_notification','TestingPurpose.notification_test');
Route::post('send_notification','TestController@SendNotification');

Route::get('checkstripe','TestController@stripePost');
// Route::get('getstripe', [StripeController::class, 'stripe']);
// Route::post('stripe', [StripeController::class, 'stripePost'])->name('stripe.post');

Route::get('TestSMS', 'TestController@CheckSMS');

// Route::get('CheckPromoCode','');
