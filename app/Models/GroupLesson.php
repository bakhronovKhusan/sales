<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupLesson extends Model{

    protected $table = 'group_lesson';

    protected $fillable = [
		'group_id',
		'lesson_id',
		'status',
		'title',
		'date',
		'lesson_type',
	];

    public $timestamps = false;

	public function group(){
		return $this->belongsTo('App\Models\Group');
	}

	public function lesson(){
		return $this->belongsTo('App\Models\Lesson');
	}
}
