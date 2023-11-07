<?php

namespace App\Http\Controllers;

use App\Models\Level;
use App\Services\StudentListService;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    private StudentListService $service;

    public function __construct(StudentListService $service)
    {

        $this->service = $service;
    }

    public function in_group($branch_id, Request $request){
//        if(!auth()->user()->can('show waiting')){
//            return response()->json(['error' => 'This page is forbidden for you'],403);
//        }
        return $this->service->list($request);
    }
    public function levels_with_students_trial($branch_id,Request $request){
//        if(!auth()->user()->can('show waiting')){
//            return response()->json(['error' => 'This page is forbidden for you'],403);
//        }

        return Level::join("courses","levels.course_id","=","courses.id")
            ->join("company_course","company_course.course_id","=","courses.id")
            ->where("company_course.company_id",1)
            ->select("levels.id","levels.name")
            ->with(['students_in_waiting_trial' => function($q) use ($branch_id,$request){
                $q->where('level_student.branch_id',$branch_id);
                $q->with("comments");
                if ($request->call)
                    $q->where('level_student.call_count',$request->call);
                if ($request->sms)
                    $q->where('level_student.sms_count',$request->sms);
                $q->orderBy('level_student.created_at','DESC');
            }])
            ->get();
    }

    public function get_levels(Request $request){
        $subdomain = 'app';
        return Level::with('course')
            ->whereHas('course', function($q) use ($subdomain){
                $q->whereHas('companies',function($q) use ($subdomain){
                    $q->where('subdomain',$subdomain);
                });
            })
            ->toSql();
    }
}
