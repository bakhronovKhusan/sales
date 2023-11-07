<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Wildside\Userstamps\Userstamps;

class Level extends Model
{
    use Userstamps;

    protected $fillable = [
        'name',
        'course_id',
        'teaches_2_teachers',
        'image',
        'description',
        'payment_for_trial',
        'has_trial_payment',
    ];

    public function course()
    {
        return $this->belongsTo('App\Models\Course');
    }

    public function groups()
    {
        return $this->hasMany('App\Models\Group');
    }

    public function prices()
    {
        return $this->hasMany('App\Models\PriceList');
    }

    public function students()
    {
        return $this->belongsToMany('App\Models\Student', 'level_student')->withPivot(
            'student_time',
            'days'
        );
    }

    public function students_in_lead()
    {
        return $this->belongsToMany('App\Models\Student', 'level_student')
            ->withPivot('student_time', 'days')
            ->wherePivot('status', 'l');
    }

    public function students_in_contacted()
    {
        return $this->belongsToMany('App\Models\Student', 'level_student')
            ->withPivot('student_time', 'days', 'comment', 'branch_id')
            ->wherePivot('status', 'c');
    }

    public function students_in_waiting()
    {
        return $this->belongsToMany('App\Models\Student', 'level_student')
            ->withPivot('student_time', 'days', 'comment','comments','administrator_id','created_at','updated_at','call_count','sms_count')
            ->wherePivot('status', 'w');
    }

    public function students_in_waiting_trial()
    {
        return $this->belongsToMany('App\Models\Student', 'level_student')
            ->withPivot('student_time', 'days', 'comment','comments','administrator_id','created_at','updated_at', 'trial_info','call_count','sms_count')
            ->wherePivot('status', 'wt');
    }

    public function students_in_waiting_active()
    {
        return $this->belongsToMany('App\Models\Student', 'level_student')
            ->withPivot('student_time', 'days', 'comment','comments','administrator_id','created_at','updated_at', 'trial_info','call_count','sms_count')
            ->wherePivot('status', 'wa');
    }

    public function students_in_leading()
    {
        // return $this->belongsToMany('App\Models\Student', 'leads')
        //     ->withPivot('staff_id', 'by', 'status')
        //     ->wherePivot('status', '1');


    }


    public function materials()
    {
        return $this->hasMany('App\Models\Material');
    }

    public function get_levels_by_course($group_id)
    {
        $sql = "
            SELECT
                id,
                name
            FROM
                t_levels
            WHERE
                course_id = (
                    SELECT
                        course_id
                    FROM
                        t_levels
                    WHERE id = (
                        SELECT level_id FROM t_groups WHERE id = ?
                    )
                )
        ";

        return DB::select($sql, [$group_id]);
    }

    public function exam_materials()
    {
        return $this->hasMany('App\Models\ExamMaterial');
    }
}
