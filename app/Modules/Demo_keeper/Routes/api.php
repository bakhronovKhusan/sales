<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\StudentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

Route::group(['middleware' => 'redis.token.check'], function ($router) {
    Route::group(['prefix' => 'v1/demo_keeper'], function ($router) {
        Route::get('get_all_branches_with_group_count', [BranchController::class,  'get_all_branches_with_group_count']);
        Route::get('levels_with_students',              [LevelController::class,   'levels_with_students']);
        Route::get('students_lead',                     [StudentController::class, 'students_lead']);
    });
});
