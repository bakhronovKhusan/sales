<?php

namespace App\Modules\Demo_keeper\Controllers;

use App\Enums\StudentStatus;
use App\Http\Controllers\Controller;
use App\Http\Response\BaseResponse;
use App\Models\GroupStudent;
use App\Services\StudentListService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Redirect;

class DemoController extends Controller
{
    private StudentListService $service;

    public function __construct(StudentListService $service)
    {

        $this->service = $service;
    }
    public function newStudentsList(Request $request){
        return BaseResponse::success($this->service->list($request));
    }

    public function activate($group_id, $student_id)
    {
        $student = GroupStudent::where('group_id',$group_id)->where('student_id',$student_id);
        if($student->first()->update(['status'=>StudentStatus::NOT_PAYING_AFTER_1_LESSON])){
            return BaseResponse::success('Change status to NOT_PAYING successfully!');
        }
    }

    public function de_activate($group_id, $student_id)
    {
        $student = GroupStudent::where('group_id',$group_id)->where('student_id',$student_id);
        if($student->first()->update(['status'=>StudentStatus::WAITING_TRIAL])){
            return BaseResponse::success('Change status to WAITING_TRIAL successfully!');
        }
    }
}
