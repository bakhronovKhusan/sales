<?php

namespace App\Http\Controllers;

use App\Models\Level;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    public function levels_with_students($branch_id, Request $request){
//        if(!auth()->user()->can('show waiting')){
//            return response()->json(['error' => 'This page is forbidden for you'],403);
//        }
        $q=Level::join("courses","levels.course_id","=","courses.id")
            ->join("company_course","company_course.course_id","=","courses.id")
            ->where("company_course.company_id",1)
            ->select("levels.*")
            ->with(['students_in_waiting' => function($q) use ($branch_id,$request){
                $q->where('level_student.branch_id',$branch_id);
                $q->with("comments");
                if ($request->call)
                    $q->where('level_student.call_count',$request->call);
                if ($request->sms)
                    $q->where('level_student.sms_count',$request->sms);
                $q->orderBy('level_student.created_at','DESC');
            }])->get();
        return $q;
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
}
