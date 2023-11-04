<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function students_lead($branch_id)
    {
//        if (auth()->user()->hasRole('Senior manager') || auth()->user()->hasRole('Manager') || auth()->user()->hasRole('IT Manager') || auth()->user()->hasRole('Analytic')) {
            $administrators = User::role(['Administrator', 'Manager', 'Senior manager'])
                ->whereHas('staff', function ($q) use ($branch_id) {
                    $q->whereHas('branches', function ($qq) use ($branch_id) {
                        $qq->where('id', $branch_id);
                    });
                })
                ->where(['type' => 'staff'])
                ->with('staff')
                ->pluck('type_id')->toArray();
            dd($administrators);
            $sts = Student::join("leads", "leads.student_id", "=", "students.id")
                ->join("staff", "leads.staff_id", "=", "staff.id")
                ->where([
                    'leads.status' => 1,
                    'leads.by' => 2,
                ])
                ->whereIn("leads.staff_id", $administrators)
                ->select("students.*", "staff.name as staff")
                ->get();

//        } // todo optimize to join
//        else {
//            $sts = Student::join("leads", "leads.student_id", "=", "students.id")
//                ->where([
//                    'leads.status' => 1,
//                    'leads.by' => 2,
//                    'leads.staff_id' => auth()->user()->type_id
//                ])
//                ->select("students.*")
//                ->get();
//        }
        return $sts;
    }
}
