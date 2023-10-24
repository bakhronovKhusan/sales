<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class Salary extends Model{
	use Userstamps;

    protected $table = 'salary';

    protected $fillable = [
		'staff_id', 'cash', 'plastic_card', 'bonus', 'total', 'comment', 'period_from', 'period_till', 'payment_method', 'branch_id',
	];

	public function staff(){
		return $this->belongsTo('App\Models\Staff');
	}

	public function branch(){
		return $this->belongsTo('App\Models\Branch');
	}

	public function get_salary_for($from,$till,$branch_id){
		$sql = "
			SELECT
				s.created_at as `date`,
				st.name as staff_name,
				s.cash as cash,
				s.plastic_card as plastic_card,
				s.bonus as bonus,
				s.total as total,
				s.comment as comment
			FROM ".DB::getTablePrefix()."salary s
			LEFT JOIN ".DB::getTablePrefix()."staff st ON s.staff_id = st.id
			WHERE
				s.created_at BETWEEN ? and ?
				AND s.deleted_at IS NULL
				AND st.deleted_at IS NULL
				AND s.branch_id = ?
		";

		return DB::select($sql,[$from,$till.' 23:59:59',$branch_id]);
	}
}
