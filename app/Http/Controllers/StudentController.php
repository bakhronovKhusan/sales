<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\User;

class StudentController extends Controller
{
    public function show($id)
    {
        return Student::with(['creator.staff', 'creator.student', 'editor.staff', 'editor.student', 'levels', 'groups.level', 'groups.teachers', 'groups.branch', 'last_track', 'tracks', 'branch', 'real_active_in_groups.level', 'real_active_in_groups.branch', 'real_active_in_groups.teachers'])->whereId($id)->first();
    }
}
