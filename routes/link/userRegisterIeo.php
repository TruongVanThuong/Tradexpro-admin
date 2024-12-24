<?php

use Illuminate\Support\Facades\Route;

Route::group(['group' => 'user_register_ieo_list'], function () {
    Route::get('user-register-ieo-list', 'UserRegisteredIeoController@adminUserRegisteredIeoList')->name('adminUserRegisteredIeoList');
    Route::get('user-register-ieo-edit/{id}', 'UserRegisteredIeoController@adminUserRegisteredIeoEdit')->name('adminUserRegisteredIeoEdit');
    Route::post('user-register-ieo-save-process', 'UserRegisteredIeoController@adminUserRegisteredIeoSaveProcess')->name('adminUserRegisteredIeoSaveProcess');
});
