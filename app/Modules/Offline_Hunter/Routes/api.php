<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\StudentController;
use App\Modules\Offline_Hunter\Controllers\OfflineController;
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
    Route::group(['prefix' => 'v1/offline_hunter'], function ($router) {
        Route::get('new_student_list/{branch_id}',[GroupController::class, 'getGroupsWhichHasNewStudents']);
    });
});
