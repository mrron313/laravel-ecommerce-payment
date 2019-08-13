<?php

Route::post('otp-verify', 'Api\AccountKitController@smsVerify')->name('api.otp-verify');

