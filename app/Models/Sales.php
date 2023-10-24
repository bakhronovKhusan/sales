<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class Sales extends Model{
	use Userstamps;

    protected $table = 'sales';

    protected $fillable = [
		'student_id', 'product_id', 'price', 'quantity', 'sum', 'branch_id',
	];

	public function branch(){
		return $this->belongsTo('App\Models\Branch');
	}

	public function student(){
		return $this->belongsTo('App\Models\Student');
	}

	public function product(){
		return $this->belongsTo('App\Models\Product');
	}

	public function get_sales_for($from,$till,$branch_id){
		$sql = "
			SELECT
				s.created_at as `date`,
				s.sum as sum,
				st.name as student_name,
				p.name as product_name
			FROM ".DB::getTablePrefix()."sales s
			LEFT JOIN ".DB::getTablePrefix()."students st ON s.student_id = st.id
			LEFT JOIN ".DB::getTablePrefix()."products p ON s.product_id = p.id
			WHERE
				s.created_at BETWEEN ? and ?
				AND s.deleted_at IS NULL
				AND st.deleted_at IS NULL
				AND p.deleted_at IS NULL
				AND s.branch_id = ?
			GROUP BY `date`, s.sum, st.name, p.name
		";

		return DB::select($sql,[$from,$till.' 23:59:59',$branch_id]);
	}
}
