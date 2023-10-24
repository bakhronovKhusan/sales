<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class ExamDates extends Model{
    use Userstamps;

	const EXAM_TYPE_MID_TERM = 1;
	const EXAM_TYPE_FINAL_TERM = 2;

    protected $table = 'exam_dates';

    protected $fillable = [
		'group_id', 'level_id', 'exam_type',  'exam_date','examiners'
	];

	protected $casts = [
        'examiners' => 'array',
    ];

	public function group(){
		return $this->belongsTo('App\Models\Group')->orderBy('time','ASC');
	}

	public function level(){
		return $this->belongsTo('App\Models\Level');
	}

	public function examiners()
	{

	}
}
