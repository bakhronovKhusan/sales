<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class StudentAttendance extends Model{
	use Userstamps;

    protected $table = 'student_attendance';

    protected $fillable = [
		'student_id', 'group_id', 'teacher_id', 'status', 'homework', 'branch_id', 'attend_date', 'comment'
	];

	public function student_attendance_teacher($teacher_id,$from,$till){
		$sql = "
            SELECT
                COUNT(DISTINCT sa.student_id) as count_student,
                DATE_FORMAT(sa.updated_at,'%Y-%m-%d') as day,
                g.id as group_id,
                g.name as group_name,
                s.id as tchr_id,
                s.name as teacher_name,
                pl.price_for_month AS price_for_month,
                CASE
                   WHEN gs.invited_teacher = ? THEN s.salary_from_invited
                   WHEN g.type = 'mini' THEN s.salary_mini
                   WHEN g.type = 'pair' THEN s.salary_pair
                   WHEN g.type = 'individual' THEN s.salary_individual
                   ELSE s.salary
                END AS percent,
                (ROUND(pl.price_for_month/
                    CASE
                        WHEN g.days = 'mwf' THEN 12
                        WHEN g.days = 'tts' THEN 12
                        WHEN g.days = 'ss' THEN 8
                        WHEN g.days = 'ed' THEN 24
                    END
                )*COUNT(DISTINCT sa.student_id)) as dayly_payment,
                (ROUND(pl.price_for_month/
                    CASE
                        WHEN g.days = 'mwf' THEN 12
                        WHEN g.days = 'tts' THEN 12
                        WHEN g.days = 'ss' THEN 8
                        WHEN g.days = 'ed' THEN 24
                    END
                    *COUNT(DISTINCT sa.student_id)*(
                        CASE
                            WHEN gs.invited_teacher = ? THEN s.salary_from_invited
                            WHEN g.`type` = 'mini' THEN s.salary_mini
                            WHEN g.`type` = 'pair' THEN s.salary_pair
                            WHEN g.`type` = 'individual' THEN s.salary_individual
                            ELSE s.salary
                        END
                        /100))) as dayly_salary
            FROM t_student_attendance sa
            LEFT JOIN ".DB::getTablePrefix()."groups g ON g.id = sa.group_id
            LEFT JOIN ".DB::getTablePrefix()."group_student gs ON gs.student_id = sa.student_id AND gs.group_id = g.id
            LEFT JOIN ".DB::getTablePrefix()."levels l ON l.id = g.level_id
            LEFT JOIN ".DB::getTablePrefix()."price_list pl ON pl.level_id = l.id AND pl.type = g.type
            LEFT JOIN ".DB::getTablePrefix()."staff s ON s.id = sa.teacher_id
            WHERE
                sa.status IN('p','a')
                AND (
                    sa.updated_at >= ? AND sa.updated_at <= ?
                )
                AND s.id = ?
                AND g.deleted_at IS NULL
                AND l.deleted_at IS NULL
                AND s.deleted_at IS NULL
            GROUP BY group_id, group_name, tchr_id, teacher_name, `day`, price_for_month, gs.invited_teacher, s.salary_from_invited, s.salary_mini, s.salary_pair, s.salary_individual, g.type, s.salary, g.days, gs.invited_teacher
        ";


        return DB::select($sql,[$teacher_id,$teacher_id,$from,$till,$teacher_id]);
	}

	public function student(){
		return $this->belongsTo('App\Models\Student')->withDefault();
	}

	public function group(){
		return $this->belongsTo('App\Models\Group')->withDefault();
	}

	public function teacher(){
		return $this->belongsTo('App\Models\Staff')->withDefault();
	}

	public function branch(){
		return $this->belongsTo('App\Models\Branch')->withDefault();
	}
}
