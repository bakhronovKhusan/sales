<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class Staff extends Model
{
    use Userstamps;

    const EMPLOYEE_TYPE_FULL_TIME_2X = 0;
    const EMPLOYEE_TYPE_FULL_TIME = 1;
    const EMPLOYEE_TYPE_PART_TIME = 2;

    protected $table = 'staff';

    protected $fillable = [
        'name',
        'nickname',
        'phone',
        'photo',
        'date_of_birth',
        'certificate',
        'degree',
        'plastic_card',
        'teaches',
        'week_working_days',
        'employment_type',
        'branch_id',
        'status',
        'gender',
        'tutor',
        'experience',
        'graduated_with_success',
        'max_group_number',
        'group_type',
        'retention',
        'work_days',
        'schedule',
        'staff_info',
        'staff_file',
        'shift',
        'workly_employee_id'
    ];

    protected $casts = [
        'schedule' => 'array',
    ];

    public function groups()
    {
        return $this->belongsToMany(
            'App\Models\Group',
            'group_teacher',
            'teacher_id',
            'group_id'
        )->withPivot('owner');
    }

    public function supervisor_groups(){
        return $this->hasMany(Group::class,'supervisor_id','id');
    }

    public function deleted_groups()
    {
        return $this->belongsToMany(
            Group::class,
            'deleted_group_teacher',
            'teacher_id',
            'group_id'
        );
    }

    public function attendances()
    {
        return $this->hasMany('App\Models\StaffAttendance');
    }

    public function user()
    {
        return $this->hasOne('App\Models\Models\User', 'type_id')->where('type', 'staff');
    }

    public function branches()
    {
        return $this->belongsToMany(
            'App\Models\Branch',
            'branch_staff',
            'staff_id',
            'branch_id'
        )->withPivot('main');
    }

    public function main_branch()
    {
        return $this->belongsToMany(
            'App\Models\Branch',
            'branch_staff',
            'staff_id',
            'branch_id'
        )
            ->wherePivot('main', true)
            ->withPivot('main');
    }

    public function statuses()
    {
        return $this->hasMany('App\Models\StaffStatus', 'staff_id', 'id');
    }

    public function extra_salary()
    {
        return $this->hasMany('App\Models\StaffExtraSalary', 'staff_id', 'id');
    }

    public function teacher_results()
    {
        return $this->hasMany('App\Models\TeacherResults', 'staff_id', 'id');
    }

    public function admin_extra_days()
    {
        return $this->hasMany('App\Models\AdminExtraDays', 'staff_id', 'id');
    }

    public function outgoings()
    {
        return $this->hasMany('App\Models\StaffOutgoings', 'staff_id', 'id');
    }

    public function staff_salary()
    {
        return $this->hasMany('App\Models\StaffSalary', 'staff_id', 'id')->where(
            'is_active',
            true
        );
    }

    public function calculated_salary()
    {
        return $this->hasMany('App\Models\CalculatedSalary', 'staff_id', 'id');
    }

    public function given_salary()
    {
        return $this->hasMany('App\Models\GivenSalary', 'staff_id', 'id');
    }

    public function staff_debt()
    {
        return $this->hasMany('App\Models\StaffDebt', 'staff_id', 'id');
    }
}
