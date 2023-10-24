<?php

namespace App\Http\Controllers\Hunter;

use App\Http\Controllers\Controller;
use App\Http\Response\BaseResponse;
use App\Services\StudentListService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HunterController extends Controller
{
    private StudentListService $service;

    public function __construct(StudentListService $service)
    {

        $this->service = $service;
    }
    public function newStudentsList(Request $request){
        return BaseResponse::success($this->service->list($request));
    }
}
