<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'redis.token.check'], function ($router) {
    Route::group(['prefix' => 'v1'], function ($router) {
        Route::get('get_all_branches',        [BranchController::class,  'get_all_branches_with_group_count']);//get-All-Branches
        Route::group(['prefix' => 'status'], function ($router) {
            Route::get('missed_trial/{branch_id}', [LevelController::class,   'levels_with_students_trial']); //missed_trial
            Route::get('waiting_new/{branch_id}',  [StudentController::class, 'students_lead']); //status => Waiting new
            Route::get('in_group/{branch_id}',     [LevelController::class,   'levels_with_students']); //status => IN GROUP
        });
    });
});
