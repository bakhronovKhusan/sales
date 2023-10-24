<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class Track extends Model
{
	public $connection="mysql2";
	use Userstamps;

 	protected $fillable = [
		'student_id',
		'status',
		'description',
		'staff_id',
		'branch_id'
	];

	public function student(){
		return $this->belongsTo('App\Models\Student')->withDefault();
	}

	public function branch(){
		return $this->belongsTo(Branch::class)->withDefault();
	}

}
