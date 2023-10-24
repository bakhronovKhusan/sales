<?php
namespace App\Models;

use Carbon\CarbonPeriod;
use App\Components\Helper;
use Wildside\Userstamps\Userstamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use Userstamps;
    protected $appends = ['count_lesson'];
    protected $fillable = [
        'name',
        'time',
        'days',
        'exact_days',
        'status',
        'fee',
        'type',
        'is_online',
        'is_corporate',
        'salary_for_corporate',
        'level_id',
        'branch_id',
        'selection_started_date',
        'selection_end_date',
        'group_started_date',
        'group_end_date',
        'is_finished',
        'tariff_id',
        'is_exception',
        'exception_salary_from_one_student',
        'supervisor_id',
        'tg_group_chat_id',
        'tg_group_link',
        'course_book',
        'is_only_girls',
        'is_30_plus',
        'is_vip',
        'is_closed',
        'is_kids',
        'is_important',
        'is_rus',
    ];

    protected $casts = [
        'exact_days' => 'array',
    ];

    public function getDaysOfWeek(){
        switch($this->days){
            case 'mwf':
                $daysOfWeek = ["Mon","Wed","Fri"];
                break;

            case 'tts':
                $daysOfWeek = ["Tue","Thu","Sat"];
                break;

            case 'ss':
                $daysOfWeek = ["Sat","Sun"];
                break;

            case 'ed':
                $daysOfWeek = ["Mon","Tue","Wed","Thu","Fri","Sat"];
                break;

            case 'other':
                $daysOfWeek = $this->exact_days;
                break;
        }
        return $daysOfWeek;
    }

    public function getLessonCountForRecalculate(){
        switch($this->days){
            case 'mwf':
            case 'tts':
                $lesson_days = 12;
                break;
            case 'ss':
                $lesson_days = 8;
                break;
            case 'ed':
                $lesson_days = 24;
                break;
            case 'other':{
                    if(count($this->exact_days) == 1){
                        $lesson_days = 4;
                    }
                    elseif(count($this->exact_days) == 2){
                        $lesson_days = 8;
                    }
                    elseif(count($this->exact_days) == 3){
                        $lesson_days = 12;
                    }
                    elseif(count($this->exact_days) == 4){
                        $lesson_days = 16;
                    }
                    elseif(count($this->exact_days) == 5){
                        $lesson_days = 20;
                    }
                    elseif(count($this->exact_days) == 6){
                        $lesson_days = 24;
                    }
                }
                break;
        }
        return $lesson_days;
    }

    public function getLessonCountByStaticDays(){
        switch($this->days){
            case 'mwf':
            case 'tts':
                $lesson_days = 13;
                break;
            case 'ss':
                $lesson_days = 8;
                break;
            case 'ed':
                $lesson_days = 26;
                break;
            case 'other':{
                    if(count($this->exact_days) == 1){
                        $lesson_days = 4;
                    }
                    elseif(count($this->exact_days) == 2){
                        $lesson_days = 8;
                    }
                    elseif(count($this->exact_days) == 3){
                        $lesson_days = 13;
                    }
                    elseif(count($this->exact_days) == 4){
                        $lesson_days = 16;
                    }
                    elseif(count($this->exact_days) == 5){
                        $lesson_days = 20;
                    }
                    elseif(count($this->exact_days) == 6){
                        $lesson_days = 26;
                    }
                }
                break;
        }
        return $lesson_days;
    }

    public function getLessonCountForStudentWriteOff(){
        $lesson_days = 0;
        switch($this->days){
            case 'mwf':
            case 'tts':
                $lesson_days = 12;
                break;
            case 'ed':
                $lesson_days = 24;
                break;
        }
        return $lesson_days;
    }

    public function getLessonCount($period = null){
        if($period){
            $from_date = new \DateTime($period[0]);
            $to_date = new \DateTime($period[1]);
        }
        else{
            $from_date = date("Y-m-01");
            $to_date = date("Y-m-t");
            $from_date = new \Datetime($from_date);
            $to_date = new \Datetime($to_date);
        }
        if(
            $from_date->format("Y-m-d")==$from_date->format("Y-m")."-01" &&
            $from_date->format("Y-m-t")==$to_date->format("Y-m-d")
        ){
            $group_lesson_days = $this->getDaysOfWeek();
            $number_of_lesson_days = 0;
            $period = CarbonPeriod::create($from_date, $to_date);
            foreach($period as $date){
                foreach($group_lesson_days as $group_lesson_day){
                    if($date->format("D")==$group_lesson_day){
                        $number_of_lesson_days++;
                    }
                }
            }
        }
        else{
            $number_of_lesson_days = $this->getLessonCountByStaticDays();
        }
        return $number_of_lesson_days;
    }

    public function one_lesson_price(){
        $lesson_days = 0;
        switch($this->days){
            case 'mwf':
            case 'tts':
                $lesson_days = 12;
                break;
            case 'ed':
                $lesson_days = 24;
                break;
        }
        return round($this->fee / $lesson_days,2);
    }

    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id', 'id');
    }

    public function teachers()
    {
        return $this->belongsToMany(
            'App\Models\Staff',
            'group_teacher',
            'group_id',
            'teacher_id'
        )->withPivot('owner');
    }

    public function deleted_teachers()
    {
        return $this->belongsToMany(
            'App\Models\Staff',
            'deleted_group_teacher',
            'group_id',
            'teacher_id'
        );
    }

    public function owner_teacher()
    {
        return $this->belongsToMany(
            'App\Models\Staff',
            'group_teacher',
            'group_id',
            'teacher_id'
        )
            ->withPivot('owner')
            ->wherePivot('owner', true);
    }
    public function getCountLessonAttribute()
    {
        $helper = new Helper;
        return $helper->count_lesson($this->days,$this->group_end_date,$this->exact_days);
    }

    public function students()
    {
        return $this->belongsToMany('App\Models\Student', 'group_student')
            ->withPivot(
                'status',
                'balance',
                'lessons_left',
                'created_at',
                'updated_at',
                'administrator_id',
                'exception_sum',
                'comment',
                'missed_trials',
                'activated_at'
            )
            ->orderBy('pivot_created_at', 'desc');
    }

    public function all_students_without_archive()
    {
        return $this->belongsToMany('App\Models\Student', 'group_student')
            ->withPivot(
                'status',
                'balance',
                'lessons_left',
                'created_at',
                'updated_at',
                'called',
                'comment',
                'exception_sum',
                'activated_at'
            )
            ->orderBy('pivot_status', 'asc')
            ->wherePivotIn('status', ['ar','fs','ttw'],'and','NotIn');
    }

    public function active_and_trial_students()
    {
        return $this->belongsToMany('App\Models\Student', 'group_student')
            ->withPivot(
                'status',
                'balance',
                'lessons_left',
                'created_at',
                'updated_at',
                'called',
                'comment',
                'exception_sum',
                'activated_at'
            )
            ->orderBy('pivot_status', 'asc')
            ->wherePivotIn('status', ['a','p1','np']);
    }

    public function active_in_group_students()
    {
        return $this->belongsToMany('App\Models\Student', 'group_student')
            ->withPivot('status', 'balance', 'lessons_left', 'created_at', 'updated_at', 'exception_sum','exception_status', 'activated_at')
            ->wherePivot('status', 'a');
    }
    public function trials_in_group_students()
    {
        return $this->belongsToMany('App\Models\Student', 'group_student')
            ->withPivot('status', 'balance', 'lessons_left', 'created_at', 'updated_at','exception_sum', 'activated_at')
            ->wherePivot('status', 'iG');
    }

    public function only_in_group_students()
    {
        return $this->belongsToMany('App\Models\Student', 'group_student')
            ->withPivot('status', 'balance', 'lessons_left', 'created_at', 'updated_at','exception_sum', 'activated_at')
            ->wherePivot('status', 'iG');
    }

    public function frozen_in_group_students()
    {
        return $this->belongsToMany('App\Models\Student', 'group_student')
            ->withPivot('status', 'balance', 'lessons_left', 'created_at', 'updated_at','exception_sum', 'activated_at')
            ->wherePivot('status', 'f');
    }

    public function not_paying_in_group_students()
    {
        return $this->belongsToMany('App\Models\Student', 'group_student')
            ->withPivot('status', 'balance', 'lessons_left', 'created_at', 'updated_at','exception_sum', 'activated_at')
            ->wherePivot('status', 'np');
    }
    public function p1_in_group_students()
    {
        return $this->belongsToMany('App\Models\Student', 'group_student')
            ->withPivot('status', 'balance', 'lessons_left', 'created_at', 'updated_at','exception_sum', 'activated_at')
            ->wherePivot('status', 'p1');
    }

    public function in_group_students()
    {
        return $this->belongsToMany('App\Models\Student', 'group_student')
            ->withPivot('status', 'balance', 'lessons_left', 'created_at', 'updated_at','exception_sum', 'activated_at')
            ->wherePivotIn('status', ['p1','np','iG']);
    }

    public function payments()
    {
        return $this->hasMany('App\Models\Payment');
    }

    public function rooms()
    {
        return $this->belongsToMany('App\Models\Room', 'schedule');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id','id');
    }

    public function tariff()
    {
        return $this->belongsTo('App\Models\Tariff');
    }

    public function discounts()
    {
        return $this->hasMany('App\Models\StudentDiscount')->where(
            'student_discount.status',
            '=',
            true
        );
    }

    public function supervisor()
    {
        return $this->belongsTo(Staff::class,'supervisor_id','id');
    }

    public function roadmap(){
        return $this->hasMany(GroupRoadmap::class);
    }

    public function exam_dates(){
        return $this->hasMany(ExamDates::class);
    }
    public function next_exam_date(){
        return $this->hasOne(ExamDates::class)->latest();
    }
    public function next_exams(){
        return $this->hasMany(ExamDates::class);
        // return $this->hasOne(ExamDates::class)->ofMany('exa', 'max')
    }

    public function currentWeek(){
        return $this->hasOne(GroupRoadmap::class)->orderBy("id","DESC");
    }

    public function attendances(){
        return $this->hasMany(StudentAttendance::class);
    }

    public function present_attendances(){
        return $this->hasMany(StudentAttendance::class)->where('status','p');
    }

    public function exam_results(){
        return $this->hasMany(ExamResults::class);
    }
}
