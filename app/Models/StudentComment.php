<?php
namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class StudentComment extends Model{
	use Userstamps;

    protected $table = 'student_comments';

    protected $fillable = [
		'student_id',  'comment', 'department'
	];


	public function student(){
		return $this->belongsTo('App\Models\Student')->withDefault();
	}


}
