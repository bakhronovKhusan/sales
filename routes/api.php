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
            Route::get('in_group/{branch_id}',     [LevelController::class,   'in_group']); //status => IN GROUP
        });
        Route::apiResource('student', StudentController::class);
        Route::get('get_levels', [LevelController::class, 'get_levels']);
        Route::get('get_all_branches', [BranchController::class,'get_all_branches_with_group_count']);
        Route::post('send_student_request', [StudentController::class,'send_student_request']);
        Route::get('sendRoadMap/{phone}', [StudentController::class, 'sendRoadMap']);

        Route::group(['prefix'=>'check_group'], function ($route){

        });
        Route::get('user/{user_id}/roles/branches', 'UserController@get_user_roles_and_branches');
        Route::get('branch/{branch_id}/level/{level_id}/{type}', 'GroupController@get_selection_or_group');
        Route::post('add_to_selection/{student_id}/{level_id}/{selection}', 'StudentController@add_to_selection');
        Route::post('add_to_selection_from_lead/{student_id}/{course_id}/{selection}', 'StudentController@add_to_selection_from_lead');
    });
});
