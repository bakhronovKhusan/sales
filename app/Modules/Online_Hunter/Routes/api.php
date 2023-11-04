<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\StudentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'redis.token.check'], function ($router) {
    Route::group(['prefix' => 'v1/online_hunter'], function ($router) {

    });
});
