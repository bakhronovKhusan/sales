<?php
namespace App\Models;

use App\Models\LessonTime;
use App\Models\Module;
use App\Models\SalaryType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class Company extends Model{
	use Userstamps;

    protected $table = 'company';

    protected $fillable = [
		'name', 'phone', 'photo', 'website', 'email', 'subdomain', 'about', 'lessons_per_month','idp_payment'
	];

	public function branches(){
		return $this->hasMany('App\Models\Branch');
	}

	public function modules(){
		return $this->belongsToMany(Module::class);
	}

	public function courses(){
		return $this->belongsToMany(Course::class);
	}

	public function lesson_times(){
		return $this->hasMany(LessonTime::class);
	}

	public function salary_types(){
		return $this->hasMany(SalaryType::class);
	}
}
