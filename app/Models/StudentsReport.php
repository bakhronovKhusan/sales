<?php

namespace App\Models;

use App\Models\Branch;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Model;

class StudentsReport extends Model
{

    protected $fillable = [
        'student_id',
        'group_id',
        'level_id',
        'type',
        'status',
        'reason',
        'comment',
        'staff_id',
        'branch_id',
        'returned'
    ];

    protected $casts = [
        'returned' => 'array',
    ];

    public function student()
    {
        return $this->belongsTo('App\Models\Student');
    }

    public function level()
    {
        return $this->belongsTo('App\Models\Level');
    }

    public function group()
    {
        return $this->belongsTo('App\Models\Group');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
