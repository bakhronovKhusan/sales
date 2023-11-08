<?php

namespace App\Http\Controllers;

use App\Components\Helper;
use App\Models\CourseStudent;
use App\Models\Group;
use App\Models\GroupStudent;
use App\Models\Branch;
use App\Models\Level;
use App\Models\LevelStudent;
use App\Models\Student;
use App\Models\StudentRequest;
use App\Models\Track;
use App\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function show($id)
    {
        return Student::with(['creator.staff', 'creator.student', 'editor.staff', 'editor.student', 'levels', 'groups.level', 'groups.teachers', 'groups.branch', 'last_track', 'tracks', 'branch', 'real_active_in_groups.level', 'real_active_in_groups.branch', 'real_active_in_groups.teachers'])->whereId($id)->first();
    }

    public function send_student_request(Request $request)
    {
//        if (auth()->user()->can('send student request') || auth()->user()->can('add complaint to student')) {

            $this->validate($request, [
                'type' => 'required',
                'student_id' => 'required',
                'branch_id' => 'required',
            ]);

            StudentRequest::create([
                "student_id" => $request->student_id,
                "branch_id" => $request->branch_id,
                "sender_id" => auth()->user()->type_id,
                "request_type" => $request->type,
                "comment_sender" => $request->comment,
            ]);

            if (($request->type == 'ar') || ($request->type == 'w') || ($request->type == 'o') || ($request->type == 'b')) {
                $student = Student::select('name', 'phone')->whereId($request->student_id)->first();
                $st_user = $student->name . ' (+998' . $student->phone . ')';
                $br = Branch::whereId($request->branch_id)->first();

                $type = match ($request->type) {
                    'ar' => 'Archive',
                    'fr' => 'Freeze',
                    'a' => 'Analyze',
                    'b' => 'Balance',
                    'g' => 'Grant',
                    'o' => 'Other',
                    "w" => "Waiting",
                    "l" => "Letter",
                    "c" => "Cashback",
                    "t" => "From teacher",
                    "p" => "Personal account access",
                };
                $data = [
                    "feedback_request" => 1,
                    "sender" => auth()->user()->staff->name,
                    "student" => $st_user,
                    "branch" => $br->name,
                    "type" => $type,
                    "comment" => $request->comment,
                ];

                // return ($data);
                $curl = \curl_init('https://supportbot.cambridgeonline.uz/index.php');
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                $output = \curl_exec($curl);
                \curl_close($curl);
//            }

            return 'done';
        } else {
            return 'no';
        }
    }

    public function roadmap(Level $level, Student $student){
        return PDF::setOptions(['debug' => true,
                                'isHtml5ParserEnabled' => true,
                                'isRemoteEnabled' => true])->loadView('roadmap',
                                compact('level', 'student'))->stream();
    }

    public function sendRoadMap(Request $request){
        (new Helper())->send_sms($request->phone,'RoadMap url: https://sales-api.cambridgeonline.uz/roadmap/1/80075');
    }

    public function add_to_selection_from_lead($student_id, $course_id, $selection)
    {

        if (!auth()->user()->can('add to selection')) {
            return response()->json(['error' => 'This page is forbidden for you'], 403);
        }

        $gs = GroupStudent::where([
            'group_id' => $selection,
            'student_id' => $student_id,
        ])->count();

        if ($gs) {
            return response()->json(['error' => "There student is already added to this group"], 422);
        }

        $group = Group::with(['level', 'teachers', 'branch'])->whereId($selection)->first();
        $group->students()->attach($student_id, [
            'created_at' => now(),
            'administrator_id' => auth()->user()->type_id,
            'created_by' => auth()->user()->id,
            'status' => 'iG'
        ]);

        CourseStudent::where(['course_id' => $course_id, 'student_id' => $student_id])->delete();

        $teacher = '';
        if (count($group->teachers)) {
            $teacher = $group->teachers[0]->name;
        }

        Track::create([
            'student_id' => $student_id,
            'status' => 's',
            'description' => 'Added to the selection #' . $group->id . ' ' . $group->level->name . ' ' . $teacher . ' ' . $group->branch->name
        ]);

        return 'done';
    }
    public function add_to_selection($student_id, $level_id = 0, $selection, $course_id = null)
    {

        if (!auth()->user()->can('add to selection')) {
            return response()->json(['error' => 'This page is forbidden for you'], 403);
        }

        $gs = GroupStudent::where([
            'group_id' => $selection,
            'student_id' => $student_id,
        ])->count();

        if ($gs) {
            return response()->json(['error' => "There student is already added to this group"], 422);
        }
        $level_admin = NULL;
        if ($level_id)
            $level_admin = LevelStudent::where(['level_id' => $level_id, 'student_id' => $student_id])->first();

        $group = Group::with(['level', 'teachers', 'branch'])->whereId($selection)->first();
        $group->students()->attach($student_id, [
            'created_at' => now(),
            'created_by' => auth()->user()->id,
            'status' => 'iG',
            'administrator_id' => isset($level_admin->administrator_id) ?? null,
        ]);

        if ($level_id)
            LevelStudent::where(['level_id' => $level_id, 'student_id' => $student_id])->delete();

        $teacher = '';
        if (count($group->teachers)) {
            $teacher = $group->teachers[0]->name;
        }

        Track::create([
            'student_id' => $student_id,
            'status' => 's',
            'description' => 'Added to the selection #' . $group->id . ' ' . $group->level->name . ' ' . $teacher . ' ' . $group->branch->name
        ]);

        return 'done';
    }
}
