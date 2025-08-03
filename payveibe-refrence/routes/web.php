<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SimController;
use App\Http\Controllers\TextVerifiedController;
use App\Http\Controllers\ViopController;
use App\Http\Controllers\WorldNumberController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\XtrapayProcessController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/proxy/prices', function (Illuminate\Http\Request $request) {
    // Get the 'country' query parameter
    $country = $request->query('country');

    // Make the request to the 5sim API from the Laravel server
    $response = Http::get('https://5sim.net/v1/guest/prices', [
        'country' => $country,
    ]);

    return $response->json();
});

//Clear Config cache:
Route::get('/clear1', function() {
    $exitCode = Artisan::call('config:cache');
    return '<h1>Clear Config cache cleared</h1>';
});


Route::get('/clear2', function() {
    $exitCode = Artisan::call('config:clear');
    return '<h1>Clear config cleared</h1>';
});

Route::any('getInitialCountdown',  [HomeController::class,'getInitialCountdown']);

Route::get('/search-viop-services', [ViopController::class, 'searchservices']);





//auth

Route::get('/',  [HomeController::class,'index']);


Route::post('login_now',  [HomeController::class,'login']);
// Route::get('login',  [HomeController::class,'login_index']);
Route::get('login',  [HomeController::class,'login_index'])->name('login');
Route::post('register_now',  [HomeController::class,'register']);
Route::get('register',  [HomeController::class,'register_index']);

Route::get('cworld',  [SimController::class,'index']);
Route::post('buy-csms',  [SimController::class,'order_csms']);
Route::get('c-sms',  [SimController::class,'delete_sms']);
Route::get('admin-c-sms',  [SimController::class,'admin_delete_sms']);
Route::get('get-csms',  [SimController::class,'get_c_sms']);



Route::get('log-out',  [HomeController::class,'logout']);
Route::post('reset-password-now',  [HomeController::class,'reset_password_now']);
Route::post('reset-password',  [HomeController::class,'reset_password']);
Route::get('expired',  [HomeController::class,'expired']);
Route::get('verify-password',  [HomeController::class,'verify_password']);
Route::get('forgot-password',  [HomeController::class,'forget_password']);
Route::get('faq',  [HomeController::class,'faq']);
Route::get('terms',  [HomeController::class,'terms']);
Route::get('policy',  [HomeController::class,'policy']);
Route::get('rules',  [HomeController::class,'rules']);
Route::post('update-password-now',  [HomeController::class,'update_password_now']);

Route::get('verify-account-view',  [HomeController::class,'verify_account_now_view']);
Route::get('verify-account-now',  [HomeController::class,'verify_account_now']);

Route::get('verify-account-now-success',  [HomeController::class,'verify_account_now_page']);


Route::any('update-smspool-rate',  [AdminController::class,'update_smspool_rate']);
Route::any('update-smspool-cost',  [AdminController::class,'update_smspool_cost']);



Route::any('get-smscode',  [HomeController::class,'get_smscode']);





Route::group(['middleware' => ['auth', 'user', 'session.timeout']], function () {

    Route::get('us',  [HomeController::class,'home']);

    Route::any('home',  [HomeController::class,'home']);
    Route::any('world',  [WorldNumberController::class,'home']);
    Route::any('check-av',  [WorldNumberController::class,'check_av']);
    Route::post('order_now',  [WorldNumberController::class,'order_now']);
    Route::any('get-smscodeworld',  [WorldNumberController::class,'get_smscode']);
    Route::any('cancleworld-sms',  [WorldNumberController::class,'cancleworld_sms']);


    Route::post('server1_order',  [HomeController::class,'server1_order']);



    Route::get('server3',  [ViopController::class,'index']);



    Route::post('viop-buy',  [ViopController::class,'viop_buy']);

    Route::any('get-viopsms',  [ViopController::class,'get_viopsms']);
    Route::any('cancle-viop',  [ViopController::class,'cancle_viop']);




    Route::any('orders',  [HomeController::class,'orders']);






    Route::any('receive-sms',  [HomeController::class,'receive_sms']);



    Route::any('delete-order',  [HomeController::class,'delete_order']);
    Route::any('delete-w-order',  [HomeController::class,'delete_w_order']);
    Route::any('admin-cancle-sms',  [HomeController::class,'admin_cancle_sms']);




    //Route::post('order-server1',  [HomeController::class,'order_now']);

    Route::any('check-sms',  [HomeController::class,'check_sms']);


    Route::get('welcome',  [HomeController::class,'welcome_index']);
    Route::get('fund-wallet',  [HomeController::class,'fund_wallet']);
    Route::get('profile',  [HomeController::class,'profile']);
    Route::post('fund-now',  [HomeController::class,'fund_now']);
    Route::post('fund-now-xtrapay', [HomeController::class,'fund_now_xtrapay']);
    
    Route::get('xtrapay/verify/{reference}', [XtrapayProcessController::class,'requery']);
    Route::get('verify',  [HomeController::class,'verify_payment']);
    Route::get('verifypay',  [HomeController::class,'verifypay_payment']);

    Route::get('resolve-page',  [HomeController::class,'resloveDeposit']);
    Route::any('resolve-now',  [HomeController::class,'resolveNow']);
    Route::get('change-password',  [HomeController::class,'change_password']);



    Route::any('update-sim-rate',  [AdminController::class,'update_sim_rate']);
    Route::any('update-sim-cost',  [AdminController::class,'update_sim_cost']);


    Route::any('update-viop-rate', [ViopController::class, 'update_viop_rate']);
    Route::any('update-viop-cost', [ViopController::class, 'update_viop_cost']);

    Route::any('update-tiger-rate', [AdminController::class, 'update_tiger_rate']);
    Route::any('update-tiger-cost', [AdminController::class, 'update_tiger_cost']);





    Route::get('server4',  [TextVerifiedController::class,'index']);




});





























//admin
Route::get('admin',  [AdminController::class,'index']);

Route::get('admin-dashboard',  [AdminController::class,'admin_dashboard']);


Route::any('update-rate',  [AdminController::class,'update_rate']);
Route::any('update-cost',  [AdminController::class,'update_cost']);

Route::get('manual-payment',  [AdminController::class,'manual_payment_view']);
Route::any('verify-payment',  [AdminController::class,'approve_payment']);
Route::any('update-acct-name',  [AdminController::class,'update_acct_name']);
Route::any('delete-payment',  [AdminController::class,'delete_payment']);



Route::any('fund-manual-now',  [HomeController::class,'fund_manual_now']);
Route::any('confirm-pay',  [HomeController::class,'confirm_pay']);


Route::get('search-user',  [AdminController::class,'search_user']);
Route::any('search-username',  [AdminController::class,'search_username']);

Route::any('about-us',  [HomeController::class,'about_us']);
Route::any('policy',  [HomeController::class,'policy']);














Route::get('users',  [AdminController::class,'index_user']);
Route::get('view-user',  [AdminController::class,'view_user']);
Route::any('update-user',  [AdminController::class,'update_user']);
Route::any('remove-user',  [AdminController::class,'remove_user']);




Route::post('edit-front-pr',  [AdminController::class,'edit_front_product']);





Route::post('admin-login',  [AdminController::class,'admin_login']);

















//product

Route::post('buy-now',  [ProductController::class,'buy_now']);
Route::post('item-view',  [ProductController::class,'item_view']);

Route::get('item-view',  [ProductController::class,'i_view']);

Route::get('allcatproduct',  [ProductController::class,'view_all_product']);

Route::post('add-new-product',  [ProductController::class,'add_new_product']);

Route::post('add-front-product',  [ProductController::class,'add_front_product']);

Route::get('detete-front-product',  [ProductController::class,'delete_front_product']);


Route::post('edit-new-product',  [ProductController::class,'edit_front_product']);


//Route::get('view-all',  [ProductController::class,'view_all_product']);


Route::post('/telegram', 'TelegramBotController@handle');


































Route::any('server3-order',  [HomeController::class,'server3_order']);
































