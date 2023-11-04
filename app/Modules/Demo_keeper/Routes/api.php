<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\StudentController;
use App\Modules\Demo_keeper\Controllers\DemoController;
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
        Route::group(['prefix' => 'v1/status'], function ($router) {
            Route::get('waiting_new/{branch_id}',  [DemoController::class, 'students_lead']); //status => Waiting new
        });
    });
});
