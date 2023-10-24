<?php

namespace App\Models;

use App\Models\Group;
use App\Models\Level;
use App\Models\Student;
use Illuminate\Database\Eloquent\Model;

// use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class TrialStudent extends Model
{
    use Userstamps;

    public const GROUP_TYPE_ACTIVE = 1;
    public const GROUP_TYPE_TRIAL = 2;

    public const REASON_TEACHER_ISNT_GOOD = 1;
    public const REASON_UNSUITABLE_LEVEL = 2;
    public const REASON_LANGUAGE_USAGE = 3;
    public const REASON_MORE_STUDENTS = 4;
    public const REASON_MORE_BOYS_GIRLS = 5;
    public const REASON_HIGH_PRICE = 6;
    public const REASON_HURRYING = 7;
    public const REASON_WRONG_NUMBER = 8;
    public const REASON_UNSUITABLE_TIME = 9;
    public const REASON_HEALTH_PROBLEMS = 10;
    public const REASON_LOCATION = 11;
    public const REASON_OTHERS = 12;

    // protected $primaryKey = null;
    protected $table = 'trial_student';

    // protected $primaryKey = ['group_id', 'student_id'];

    protected $primaryKey = 'group_id';

    public $incrementing = false;

    protected $fillable = [
        'group_id',
        'student_id',
        'teacher_id',
        'lesson_date',
        'is_pay',
        'administrator_id',
        'pay_time',
        'is_new',
        'details',
        'level_id',
        'group_type',
        'reason',
        'is_withdraw',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

}
