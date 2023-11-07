<?php

namespace App\Services;

use App\Http\Resources\ResourceStudentList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentListService
{
    public function list(Request $request)
    {
        $date = $request->date ?? date('Y-m-d');
        $branch_id = $request->branch_id ? 'and t_groups.branch_id = '.$request->branch_id :'';
        $results =  DB::select('SELECT
                                        t_students.created_at as students_created_at,
                                        t_groups.days,
                                        t_groups.time as group_time,
                                        IFNULL(t_staff.id, "not exit!") as staff_id,
                                        t_group_student.status,
                                        t_group_student.student_id,
                                        t_group_student.group_id,
                                        CONCAT(IFNULL(t_students.name, ""), " ", IFNULL(t_students.surname, "")) as name,
                                        IFNULL(CONCAT(t_students.phone, ", ", t_students.phone2), t_students.phone) as phone,
                                        IFNULL(t_staff.certificate, "Not Exits!") as teacher_info,
                                        CASE
                                            WHEN t_group_student.status="iG" THEN "IN GROUP"
                                            WHEN t_group_student.status="wt" OR t_group_student.status="w" THEN "DONT COME"
                                            WHEN t_group_student.status="np" THEN "NOT PAID YET"
                                            WHEN t_group_student.status="a" THEN "ACTIVE"
                                        END AS status_type,
                                        CONCAT("api/v1/hunter/activate/",t_group_student.group_id,"/",t_group_student.student_id) as activate_url,
                                        CONCAT("api/v1/hunter/de-activate/",t_group_student.group_id,"/",t_group_student.student_id) as de_activate_url,
                                        CONCAT("https://app.cambridgeonline.uz/cheque/",t_group_student.group_id,"/",t_group_student.student_id) as check_url,
                                        CONCAT(
                                            CONCAT("W",
                                                IFNULL((SELECT week_num FROM t_group_roadmap WHERE
                                                      t_group_roadmap.group_id = t_group_student.group_id
                                                      ORDER by t_group_roadmap.id DESC LIMIT 1),"-new")), " ",
                                                IFNULL(t_levels.name, ""), " ",
                                                IFNULL(t_groups.time, "")) as group_info
                                    FROM t_group_student
                                    LEFT JOIN t_students ON t_students.id = t_group_student.student_id
                                    LEFT JOIN t_groups ON t_groups.id = t_group_student.group_id
                                    LEFT JOIN t_group_teacher ON t_group_teacher.group_id = t_group_student.group_id
                                    LEFT JOIN t_staff ON t_staff.id = t_group_teacher.teacher_id
                                    LEFT JOIN t_levels ON t_levels.id = t_groups.level_id
                                    WHERE t_group_student.status in ("iG")
                                      AND (t_groups.status = "a" OR t_groups.status = "s")
                                      AND (
                                            (DAYOFWEEK("'.$date.'") IN (3, 5, 7) AND t_groups.days = "tts")
                                            OR
                                            (DAYOFWEEK("'.$date.'") IN (1, 2, 4, 6) AND t_groups.days = "mwf")
                                          )
                                        and (t_groups.created_at <= CONCAT("'.$date.'", " 23:59:59") or t_groups.updated_at <= CONCAT("'.$date.'", " 23:59:59"))
                                        and t_staff.id is not null
                                        '. $branch_id .'
                                    ORDER BY t_groups.created_at DESC');
        $return = [];
        foreach ($results as $result){
            $return[$result['group_time']][] = $result;
        }
        return ResourceStudentList::collection($return);
    }
}
