<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('login', [AuthController::class, 'login']);
Route::post('userdetails', [AuthController::class, 'userdetails']);
Route::post('product_list', [AuthController::class, 'product_list']);
Route::post('add_address', [AuthController::class, 'add_address']);
Route::post('update_address', [AuthController::class, 'update_address']);
Route::post('delete_address', [AuthController::class, 'delete_address']);
Route::post('address_list', [AuthController::class, 'address_list']);
Route::post('place_order', [AuthController::class, 'place_order']);
Route::post('createOrder', [AuthController::class, 'createOrder']);
Route::post('orders_list', [AuthController::class, 'orders_list']);
Route::post('otp', [AuthController::class, 'otp']);
Route::post('my_address_list', [AuthController::class, 'my_address_list']);
Route::post('settings_list', [AuthController::class, 'settings_list']);
Route::post('reward_product_list', [AuthController::class, 'reward_product_list']);
Route::post('pincode', [AuthController::class, 'pincode']);
Route::post('appsettings_list', [AuthController::class, 'appsettings_list']);
Route::post('ship_webhook', [AuthController::class, 'ship_webhook']);
Route::post('reviews_list', [AuthController::class, 'reviews_list']);
Route::post('privacy_policy', [AuthController::class, 'privacy_policy']);
Route::post('terms_conditions', [AuthController::class, 'terms_conditions']);
Route::post('refund_policy', [AuthController::class, 'refund_policy']);
Route::post('address_details', [AuthController::class, 'address_details']);
Route::post('update_reviews', [AuthController::class, 'update_reviews']);
Route::post('update_ratings', [AuthController::class, 'update_ratings']);
Route::post('update_resells', [AuthController::class, 'update_resells']);
Route::post('cron_points', [AuthController::class, 'cron_points']);
Route::post('image_sliders', [AuthController::class, 'image_sliders']);


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
