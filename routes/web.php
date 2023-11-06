<?php

use App\Components\Helper;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/demo_keeper',function (){
    return view('demo_keeper');
});

Route::get('/offline_hunter',function (){
//    (new Helper())->send_sms('+998974639641','Testing processing!');
    return view('offline_hunter');
});

Route::get('/online_hunter',function (){
    return view('online_hunter');
});

Route::get('/roadmap/{level}/{student}',[StudentController::class, 'roadmap']);
