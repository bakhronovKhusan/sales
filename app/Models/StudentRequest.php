<?php

namespace App\Models;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class StudentRequest extends Model
{
    use HasFactory;

    use Userstamps;
    const REQUEST_ARCHIVE = 'a';
    const REQUEST_FREEZE = 'fr';
    const REQUEST_BALANCE = 'b';
    const REQUEST_ANALYZE = 'a';
    const REQUEST_WAITING = 'w';
    const REQUEST_OTHER = 'o';
    const STATUS_NEW = 'n';
    const STATUS_PROCESS = 'p';
    const STATUS_FINISHED= 'f';
    const RESULT_DONE= 1;
    const RESULT_CANCEL= 0;


    protected $table = 'student_requests';
    protected $fillable = [
        'student_id',
        'sender_id',
        'request_type',
        'status',
        'comment_sender',
        'comment_auditor',
        'receiver_id',
        'branch_id',
        'result_request',
        'finished_time',
        'need_offline',
        'letter_source',
    ];


    public function student(){
        return $this->belongsTo('App\Models\Student');
    }

    public function branch(){
        return $this->belongsTo('App\Models\Branch');
    }

    public function sender(){
        return $this->belongsTo(Staff::class,'sender_id','id');
    }

    public function receiver(){
        return $this->belongsTo(Staff::class,'receiver_id','id');
    }

}
