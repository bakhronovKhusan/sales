<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CourseStudent extends Model{
    protected $table = 'course_student';

    protected $fillable = [
        'course_id', 'student_id', 'status', 'mark', 'comment', 'branch_id',
    ];

    public function course(){
        return $this->belongsTo('App\Course');
    }

    public function student(){
        return $this->belongsTo('App\Student');
    }

    public function branch(){
        return $this->belongsTo('App\Branch');
    }

    public function getCSDates($branch_id){
        $query = "
			SELECT
				DISTINCT DATE(created_at) as signed_date,
			    course_id
			FROM
				t_course_student
			WHERE
				branch_id = :branch_id
			ORDER BY DATE(created_at)
		";

        return DB::select($query,['branch_id'=>$branch_id]);
    }

    public function getCSStudents($branch_id){
        $query = "
			SELECT
				s.name,
				s.phone,
			    cs.*
			FROM t_course_student cs
			LEFT JOIN t_students s
				ON
			    	cs.student_id = s.id
			WHERE
				cs.branch_id = :branch_id
			ORDER BY cs.status, cs.created_at
		";

        return DB::select($query,['branch_id'=>$branch_id]);
    }
}
