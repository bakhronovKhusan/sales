<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

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
}
