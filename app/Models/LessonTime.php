<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonTime extends Model{
    protected $table = 'lesson_times';

    protected $fillable = [
		'lesson_time',
		'company_id',
	];

	public function company(){
		return $this->belongsTo('App\Models\Company');
	}

}
