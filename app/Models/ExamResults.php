<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class ExamResults extends Model{
    use Userstamps;

    protected $table = 'exam_results';

    protected $fillable = [
		'group_id', 'student_id', 'score', 'comment', 'exam_date','speaking','writing','grammar','my_work','info','retake_date','presentation_point'
	];

	public function group(){
		return $this->belongsTo('App\Models\Group');
	}

	public function student(){
		return $this->belongsTo('App\Models\Student');
	}
}
