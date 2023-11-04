<?php

namespace App\Models;

use App\Models\WrongLevelStudent;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class Student extends Model
{
//    public $connection="mysql2";
    use Userstamps;
    public $absent_limit;
    protected $fillable = [
        'name',
        'surname',
        'gender',
        'photo',
        'phone',
        'phone2',
        'date_of_birth',
        'ref',
        'balance',
        'comment',
        'branch_id',
        'email',
        'address',
        'expectation',
        'degree',
        'oferta',
        'oferta_time',
        'created_by',
        'updated_by',
        'called',
        'grant',
        'grant_details',
        'representative',
        'representative_phone',
        'representative_kinship_degree',
        'target',
        'ielts_target',
        'university',
        'when_enter',
        'first_level',
        'first_level_type',
        'grammar_result',
        'speaking_level',
        'coins',
        'ltv',
        'referral',
        'tag',
        'tagged_at',
        'tagged_comment',
        'tag_id',
        'tag_expire_date',
        'is_ielts',
        'ielts_certificate',
        'is_new_payment',
        'is_mystery',

    ];

    protected $casts = [
        'grant_details' => 'array',
    ];

    public function courses()
    {
        return $this->belongsToMany('App\Models\Course')->withPivot(
            'status',
            'comment',
            'branch_id'
        );
    }

    public function levels()
    {
        return $this->belongsToMany('App\Models\Level', 'level_student')->withPivot(
            'student_time',
            'days',
            'status'
        );
    }

    public function groups()
    {
        return $this->belongsToMany('App\Models\Group', 'group_student')->withPivot(
            'status',
            'balance',
            'lessons_left',
            'comment',
            'exception_sum',
            'exception_status',
            'created_at',
            'updated_at'
        );
    }

    public function a_p1_in_groups()
    {
        return $this->belongsToMany('App\Models\Group', 'group_student')
            ->withPivot('status', 'balance', 'lessons_left', 'created_at', 'exception_sum', 'updated_at')
            ->wherePivotIn('status', ['a', 'p1']);
    }

    public function a_ig_p1_np_in_groups()
    {
        return $this->belongsToMany('App\Models\Group', 'group_student')
            ->withPivot('status', 'balance', 'lessons_left', 'created_at', 'exception_sum', 'updated_at')
            ->wherePivotIn('status', ['a', 'p1', 'iG', 'np']);
    }

    public function for_pay_groups()
    {
        return $this->belongsToMany('App\Models\Group', 'group_student')
            ->withPivot('status', 'balance', 'lessons_left', 'created_at', 'exception_sum', 'updated_at')
            ->wherePivotIn('status', ['a', 'p1', 'np']);
    }

    public function real_active_in_groups()
    {
        return $this->belongsToMany('App\Models\Group', 'group_student')
            ->withPivot('status', 'balance', 'lessons_left', 'created_at', 'exception_sum', 'exception_status', 'updated_at', 'activated_at')
            ->where('groups.status', 'a')
            ->wherePivot('status', 'a');
    }

    public function failed_in_groups()
    {
        return $this->belongsToMany('App\Models\Group', 'group_student')
            ->withPivot('status', 'balance', 'lessons_left', 'created_at', 'exception_sum', 'updated_at')
            ->where('groups.status', 'a')
            ->wherePivot('status', 'fs');
    }


    public function not_archived_in_groups()
    {
        return $this->belongsToMany('App\Models\Group', 'group_student')
            ->withPivot('status', 'balance', 'lessons_left', 'created_at', 'exception_sum', 'updated_at')
            ->wherePivot('status', '!=', 'ar');
    }

    public function trial_in_groups()
    {
        return $this->belongsToMany('App\Models\Group', 'group_student')
            ->withPivot(
                'status',
                'balance',
                'lessons_left',
                'comment',
                'called',
                'created_at',
                'updated_at',
                'exception_sum'
            )
            ->wherePivotIn('status', ['p1', 'np']);
    }

    public function in_groups_missed_1()
    {
        return $this->belongsToMany('App\Models\Group', 'group_student')
            ->withPivot('missed_lessons', 'called', 'comment', 'exception_sum')
            ->wherePivot('missed_lessons', '>=', 1)
            ->wherePivotIn('status', ['a', 'f'])
            ->where('groups.status', 'a');
    }

    public function in_groups_missed_2()
    {
        return $this->belongsToMany('App\Models\Group', 'group_student')
            ->withPivot('missed_lessons', 'exception_sum')
            ->wherePivot('missed_lessons', '>=', 2)
            ->wherePivotIn('status', ['a', 'f'])
            ->where('groups.status', 'a');
    }

    public function in_groups_missed_3()
    {
        return $this->belongsToMany('App\Models\Group', 'group_student')
            ->withPivot('missed_lessons')
            ->wherePivot('missed_lessons', '>=', 3)
            ->wherePivotIn('status', ['a', 'f']);
    }

    public function payments()
    {
        return $this->hasMany('App\Models\Payment');
    }

    public function branch()
    {
        return $this->belongsTo('App\Models\Branch')->withDefault();
    }

    public function tagged()
    {
        return $this->belongsTo('App\Models\Tag', "tag_id", "id");
    }

    public function tracks()
    {
        return $this->hasMany('App\Models\Track');
    }

    public function last_track()
    {
        return $this->hasOne('App\Models\Track')->latest();
    }

    public function attendances()
    {
        return $this->hasMany('App\Models\StudentAttendance');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\StudentComment')->latest();
    }

    public function homeworks()
    {
        return $this->hasMany('App\Models\StudentAttendance');
    }

    public function absents()
    {
        return $this->hasMany('App\Models\StudentAttendance')->where('status', 'a');
    }

    public function filter($filter)
    {
        $this->absent_limit = $filter;
        return $this;
    }
    public function getAbsentsLastAttribute()
    {
        return $this->absents()->latest()->take($this->absent_limit)->get();
    }
    public function absentsLimit3()
    {
        return $this->hasMany('App\Models\StudentAttendance')
            ->where('status', 'a')
            ->latest()
            ->limit(3);
    }

    public function sales()
    {
        return $this->hasMany('App\Models\Sales');
    }

    public function student_results()
    {
        return $this->hasMany('App\Models\TeacherResults', 'student_id', 'id');
    }


    public function student_reports()
    {
        return $this->hasMany('App\Models\StudentsReport', 'student_id', 'id')->latest();
    }

    public function user()
    {
        return $this->hasOne('App\User', 'type_id')->where('type', 'student');
    }

    public function payment_for_a_lesson()
    {
        return $this->belongsToMany('App\Models\Group', 'payment_for_a_lesson')
            ->withPivot('lesson_count', 'lesson_price', 'status')
            ->wherePivot('status', true);
    }

    public function leads()
    {
        return $this->hasMany('App\Models\Lead');
    }

    public function requests()
    {
        return $this->hasMany('App\Models\StudentRequest')->latest();
    }

    public function complaints()
    {
        return $this->hasMany('App\Models\Models\TeacherComplaint')->latest();
    }


    public function last_failed_exam()
    {
        return $this->hasOne('App\Models\ExamResults')
            ->where('comment', 'fail')->orWhere('comment', 'absent')->latest();
    }
    public function failed_exams()
    {
        return $this->hasMany('App\Models\ExamResults')
            ->where('comment', 'fail')->orWhere('comment', 'absent');
    }

    public function absent_exams()
    {
        return $this->hasMany('App\Models\ExamResults')
            ->where('comment', 'absent')->latest();
    }

    public function only_failed_exams()
    {
        return $this->hasMany('App\Models\ExamResults')
            ->where('comment', 'fail')->latest();
    }

    public function passed_exams()
    {
        return $this->hasMany('App\Models\ExamResults')
            ->where('comment', 'pass');
    }

    public function discounts()
    {
        return $this->hasMany('App\Models\StudentDiscount');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class);
    }

    public function mock_exams()
    {
        return $this->hasMany(MockExamRegister::class);
    }

    public function weekly_home_tasks()
    {
        return $this->hasMany(WeeklyHomeTasksStatus::class);
    }

    public function last_archived_in_groups()
    {
        return $this->belongsToMany('App\Models\Group', 'group_student')
            ->withPivot('status', 'balance', 'lessons_left', 'created_at', 'exception_sum', 'updated_at')
            ->wherePivot('status', '=', 'ar')->latest('updated_at');
    }

    public function wordlist_results()
    {
        return $this->hasMany('App\Models\WordlistResults');
    }
}
