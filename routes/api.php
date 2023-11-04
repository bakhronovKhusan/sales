<?php

use App\Http\Controllers\BranchController;
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

//Route::group(['middleware' => 'redis.token.check'], function ($router) {
//    Route::group(['prefix' => 'v1'], function ($router) {
////        Route::get('new-students-list/{date?}/{branch_id?}', [HunterController::class, 'newStudentsList']);
////        Route::post('status_change/{student_id}', [HunterController::class, 'changeStatus']);
////
////        Route::group(['prefix' => 'hunter'], function ($router) {
////            Route::post('activate/{group_id}/{student_id}', [HunterController::class, 'activate']);
////            Route::post('de-activate/{group_id}/{student_id}', [HunterController::class, 'de_activate']);
////        });
//
////        Route::group(['prefix' => 'branch'], function ($router) {
////            Route::get('get_all_branches_with_group_count', [BranchController::class, 'get_all_branches_with_group_count']);
////        });
//    });
//});
