<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\ViopController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\XtrapayProcessController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::any('w-webhook',  [HomeController::class,'world_webhook']);
Route::any('d-webhook',  [HomeController::class,'diasy_webhook']);
Route::any('v-webhook',  [ViopController::class,'viop_webhook']);


Route::any('e_fund',  [HomeController::class,'e_fund']);
Route::any('e_check',  [HomeController::class,'e_check']);

Route::any('verify',  [HomeController::class,'verify_username']);
Route::post('/ipn/xtrapay', [XtrapayProcessController::class, 'ipn']);
Route::get('/ipn/xtrapay/requery/{reference}', [XtrapayProcessController::class, 'checkTransaction']);


Route::any('balance',  [ApiController::class,'get_balance']);
Route::any('get-world-countries',  [ApiController::class,'get_world_countries']);
Route::any('get-world-services',  [ApiController::class,'get_world_services']);
Route::any('check-world-number-availability',  [ApiController::class,'check_availability']);
Route::any('rent-world-number',  [ApiController::class,'rent_world_number']);
Route::any('get-world-sms',  [ApiController::class,'get_world_sms']);







