<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class LevelStudent extends Model{
    use Userstamps;

    protected $table = 'level_student';

    protected $fillable = [
        'student_id', 'level_id', 'status', 'student_time', 'days', 'comment', 'comments','administrator_id','branch_id','trial_info','call_count','sms_count',
    ];
    protected $casts = [
        'trial_info' => 'array',
        'comments' => 'array',
    ];

    public function student(){
        return $this->belongsTo('App\Models\Student')->withDefault();
    }

    public function level(){
        return $this->belongsTo('App\Models\Level')->withDefault();
    }

    public function branch(){
        return $this->belongsTo(Branch::class);
    }
}
