<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    public function get_selection_or_group($branch_id,$level_id,$type){
        if ($type=='n')
        {
            $type='a';
            if ($level_id!=1)
                $level_id=$level_id-1;
        }
        $g=Group::where([
            'branch_id' => $branch_id,
            'status' => $type
        ]);

        if ($level_id==5)
        {
            $g=$g->whereIn('level_id',['5','6']);
        }
        else
        {
            $g=$g->where('level_id', $level_id);
        }
        if ($type=='s')
        {
            $g->has('only_in_group_students','<',16);
        }
        if ($type=='a')
        {
            $g->has('all_students_without_archive','<',16);
        }
        return  $g->with(['teachers','currentWeek','level','active_in_group_students','not_paying_in_group_students','p1_in_group_students','only_in_group_students'])
            ->get();
    }

    public function getGroupsWhichHasNewStudents(Request $request){
        dd($request->branch_id);

        if ($request->branch_id) {
            $numbers_query = "SELECT
                        COUNT(gs.student_id) AS `number`,
                        (
                            SELECT
                                COUNT(tgs.student_id)
                            FROM t_group_student tgs
                            LEFT JOIN  t_groups tg ON  tg.id = tgs.group_id
                            WHERE tg.branch_id = g.branch_id
                            AND tg.`status` = 'a'
                            AND tgs.`status` = 'iG'
                            AND tgs.`missed_trials` = 0
                            AND (
                                tgs.`called` = 0
                                OR tgs.`called` IS NULL
                            )
                        ) AS not_called,
                        g.branch_id
                    FROM t_group_student gs
                    LEFT JOIN t_groups g ON g.id=gs.group_id
                    WHERE
                        g.branch_id IN (" . $request->branch_id . ")
                        AND g.`status` = 'a'
                        AND gs.`status` = 'iG'
                        AND gs.`missed_trials` = 0
                    GROUP BY g.branch_id";

            $numbers = DB::select($numbers_query);


            $groups = Group::whereHas('students', function ($q) {
                $q->where('group_student.status', 'iG');
                $q->where('group_student.missed_trials', 0);
            })
                ->where('status', 'a')
                ->whereNotIn('branch_id', config("branch.not_used_branches"))
                ->whereIn('branch_id', $request->branch_id)
                ->with(['all_students_without_archive' => function ($q) {
                    $q->with("comments");
                    $q->where('group_student.status', 'iG');
                    $q->where('group_student.missed_trials', 0);
                },'currentWeek'])
                ->with(['teachers','branch','level'])
                ->orderBy('branch_id', 'ASC')
                ->orderBy('id', 'ASC')
                ->get();
            return [
                'groups' => $groups,
                'numbers' => $numbers
            ];
        }
    }
}
