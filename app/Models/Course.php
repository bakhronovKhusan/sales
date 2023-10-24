<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class Course extends Model{
	use Userstamps;

	protected $fillable = [
		'name'
	];

	public function levels(){
		return $this->hasMany('App\Models\Level');
	}

	public function bonus(){
		return $this->hasOne('App\Models\SupervisorBonus');
	}

	public function students_sign_up_for_test(){
		return $this->belongsToMany('App\Models\Student')->withPivot('status','comment');
	}

	public function companies(){
		return $this->belongsToMany(Company::class);
	}
}
