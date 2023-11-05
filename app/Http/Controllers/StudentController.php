<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Student;
use App\Models\StudentRequest;
use App\User;
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
}
