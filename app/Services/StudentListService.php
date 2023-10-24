<?php

namespace App\Services;

use App\Http\Resources\ResourceStudentList;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class StudentListService
{
    public function list(Request $request)
    {
        $result = DB::table('t_group_student')
                ->select('t_groups.created_at as group_created_at',
                DB::raw('IFNULL( t_staff.id, "not exit!") as staff_id'),
                                't_group_student.status',
                                't_group_student.group_id',
                                't_group_student.student_id',
                DB::raw('CONCAT( IFNULL(t_students.name, ""), " ", IFNULL(t_students.surname, "")) as name'),
                DB::raw('IFNULL( CONCAT(t_students.phone, ", ", t_students.phone2), t_students.phone) as phone'),
                DB::raw('IFNULL( t_staff.certificate, "Not Exits!") as teacher_info'),
                DB::raw('t_groups.time as group_time'),
                DB::raw("CASE
                                WHEN t_group_student.status='iG' THEN 'IN GROUP'
                                WHEN t_group_student.status='wt' OR t_group_student.status='w' THEN 'DONT COME'
                                WHEN t_group_student.status='np' THEN 'NOT PAID YET'
                                WHEN t_group_student.status='a' THEN 'ACTIVE'
                                ELSE '' END AS status_type"),
                DB::raw('CONCAT( CONCAT(
                                IFNULL(t_lead_report.demo_date, ""), " W",
                                IFNULL((SELECT week_num FROM t_group_roadmap WHERE
                                    t_group_roadmap.group_id = t_group_student.group_id
                                    ORDER by t_group_roadmap.id DESC LIMIT 1), "-not")), " ",
                                IFNULL(t_levels.name, ""), " ",
                                IFNULL(t_groups.time, "")) as group_info'))
                ->leftJoin('t_students', 't_students.id', '=', 't_group_student.student_id')
                ->leftJoin('t_groups', 't_groups.id', '=', 't_group_student.group_id')
                ->leftJoin('t_lead_report', 't_lead_report.student_id', '=', 't_students.id')
                ->leftJoin('t_group_teacher', 't_group_teacher.group_id', '=', 't_group_student.group_id')
                ->leftJoin('t_staff', 't_staff.id', '=', 't_group_teacher.teacher_id')
                ->leftJoin('t_levels', 't_levels.id', '=', 't_groups.level_id')
                ->where('t_group_student.status', '=', ($request->type ?? 'iG'))
                ->orderBy('group_time')
                ->orderBy('group_created_at', 'desc')
                ->get();
        return ResourceStudentList::collection($result);
    }
}
