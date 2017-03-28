<?php

Route::group(['prefix' => 'api/eyewitness/v1', 'before' => 'eyewitness_auth'], function () {

    Route::get('composer', 'Eyewitness\Eye\Http\Controllers\ComposerController@ping');

    Route::get('server', 'Eyewitness\Eye\Http\Controllers\ServerController@ping');

    Route::get('failed_queue', 'Eyewitness\Eye\Http\Controllers\FailedQueueController@index');
    Route::get('failed_queue/delete/all', 'Eyewitness\Eye\Http\Controllers\FailedQueueController@delete_all');
    Route::get('failed_queue/delete/{id}', 'Eyewitness\Eye\Http\Controllers\FailedQueueController@delete');
    Route::get('failed_queue/retry/{id}', 'Eyewitness\Eye\Http\Controllers\FailedQueueController@retry');

    Route::get('log', 'Eyewitness\Eye\Http\Controllers\LogController@index');
    Route::get('log/show', 'Eyewitness\Eye\Http\Controllers\LogController@show');
});
