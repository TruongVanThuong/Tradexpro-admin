<?php

use Illuminate\Support\Facades\Route;

Route::group(['group' => 'ieo_list'], function () {
    Route::get('ieo-list', 'ieoController@getIeo')->name('adminIeoList');
    Route::get('add-new-ieo', 'ieoController@adminAddIeo')->name('adminAddIeo');
    Route::post('save-new-ieo', 'ieoController@adminSaveIeo')->name('adminSaveIeo');
    Route::get('ieo-edit/{id}', 'ieoController@adminIeoEdit')->name('adminIeoEdit');
    Route::post('ieo-save-process', 'ieoController@adminIeoSaveProcess')->name('adminIeoSaveProcess');
    Route::get('ieo-delete/{id}', 'ieoController@adminIeoDelete')->name('adminIeoDelete');
});
