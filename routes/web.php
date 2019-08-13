<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::get('/payment-initialize', 'PaymentController@index')->name('home');
Route::get('add-to-cart/{id}', 'CartController@addToCart');

Route::post('payment/pay', 'SSLCommerzController@index')->name('payment.pay');
Route::post('payment/success', 'SSLCommerzController@paymentSuccess')->name('payment.success');
Route::post('payment/failed', 'SSLCommerzController@paymentFailed')->name('payment.failed');
Route::post('payment/cancelled', 'SSLCommerzController@paymentCancelled')->name('payment.cancelled');
Route::post('payment/ipn', 'SSLCommerzController@paymentIpn')->name('payment.ipn');

Route::get('payment/{order}/success', 'SSLCommerzController@responseSuccess')
    ->name('payment.response.success');
Route::get('payment/{order}/failed', 'SSLCommerzController@responseFailed')
    ->name('payment.response.failed');
Route::get('payment/{order}/cancelled', 'SSLCommerzController@responseCancelled')
    ->name('payment.response.cancelled');
