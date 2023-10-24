<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class Payment extends Model{
	use Userstamps;

	protected $fillable = [
		'student_id',
		'group_id',
		'price_id',
		'lesson_count',
		'discount',
		'price_for_month',
		'total_price',
		'cash',
		'plastic_card',
		'bank_account',
		'payme',
		'click',
		'cheque_sum',
		'balans',
		'online_payment',
		'humo',
		'books',
		'total_payment',
		'credit',
		'comment',
		'branch_id',
		'cheque',
		'bank_details'
	];
	protected $casts = [
        'bank_details' => 'array',
    ];

	public function student(){
		return $this->belongsTo('App\Models\Student')->withDefault();
	}

	public function group(){
		return $this->belongsTo('App\Models\Group')->withDefault();
	}

	public function price(){
		return $this->belongsTo('App\Models\PriceList')->withDefault();
	}

	public function branch(){
		return $this->belongsTo('App\Models\Branch')->withDefault();
	}

	public function get_fees_for($from,$till,$branch_id){
		$sql = "
			SELECT
				p.created_at as `date`,
				p.total_payment as fee,
				s.name as student_name,
				g.name as group_name
			FROM ".DB::getTablePrefix()."payments p
			LEFT JOIN ".DB::getTablePrefix()."students s ON p.student_id = s.id
			LEFT JOIN ".DB::getTablePrefix()."groups g ON p.group_id = g.id
			WHERE
				p.created_at BETWEEN ? and ?
				AND p.deleted_at IS NULL
				AND s.deleted_at IS NULL
				AND g.deleted_at IS NULL
				AND p.branch_id = ?
			GROUP BY `date`, p.total_payment, s.name, g.name
		";

		return DB::select($sql,[$from,$till.' 23:59:59',$branch_id]);
	}

	public function get_dayly_payments($date,$branch_id){
		$tags = implode(', ', $branch_id);
		// echo $tags;
		$sql = "
			SELECT
				SUM(p.cash) as cash,
				SUM(p.total_payment) as total_payment,
				SUM(p.plastic_card) as plastic_card,
				SUM(p.bank_account) as bank_account,
				SUM(p.payme) as payme,
				SUM(p.click) as click,
				SUM(p.cheque_sum) as cheque_sum,
				SUM(p.online_payment) as online_payment,
				SUM(p.humo) as humo,
				SUM(p.books) as books,
				SUM(p.balans) as balans,
				b.name
			FROM ".DB::getTablePrefix()."payments p
			LEFT JOIN t_branches b on p.branch_id=b.id
			WHERE DATE(p.created_at) = ?
				AND branch_id  IN  (".$tags.")
				GROUP BY b.name
		";

		return DB::select($sql,[$date]);
	}
	public function get_dayly_payments_all($date){
		$sql = "
			SELECT
				SUM(p.cash) as cash,
				SUM(p.total_payment) as total_payment,
				SUM(p.plastic_card) as plastic_card,
				SUM(p.bank_account) as bank_account,
				SUM(p.payme) as payme,
				SUM(p.click) as click,
				SUM(p.cheque_sum) as cheque_sum,
				SUM(p.online_payment) as online_payment,
				SUM(p.humo) as humo,
				SUM(p.books) as books,
				SUM(p.balans) as balans,
				b.name
			FROM ".DB::getTablePrefix()."payments p
			LEFT JOIN t_branches b on p.branch_id=b.id
			WHERE DATE(p.created_at) = ?
				GROUP BY b.name
		";

		return DB::select($sql,[$date]);
	}
	public function get_dayly_total_cheque_branch($date,$branch_id){
		$sql = "
				SELECT
					SUM(p.total_payment) as total,
					b.name
				FROM ".DB::getTablePrefix()."payments p
				LEFT JOIN t_branches b on p.branch_id=b.id
				WHERE DATE(p.created_at) = ?
					AND branch_id = ? AND p.cheque=1
					GROUP BY b.name
		";

		return DB::select($sql,[$date,$branch_id]);
	}

	public function total_period_payments_month($month,$year)
	{
		$from_date = $year."-".$month."-01";
        $to_date = date("Y-m-t",strtotime($from_date));
	$sql = "
	SELECT
		SUM(p.total_payment) as total_payment,
		DATE(p.created_at) AS d
	FROM ".DB::getTablePrefix()."payments p
	LEFT JOIN t_branches b on p.branch_id=b.id
	WHERE p.created_at BETWEEN ? and ?
		GROUP BY date(created_at) order by d
	";
	return DB::select($sql,[$from_date,$to_date.' 23:59:59']);
	}

	public function result_period_payments_month($month,$year)
	{
		$from_date = $year."-".$month."-01";
        $to_date = date("Y-m-t",strtotime($from_date));
	$sql = "
	SELECT
		SUM(p.total_payment) as total_payment,
		b.name,DATE(p.created_at) AS d
	FROM ".DB::getTablePrefix()."payments p
	LEFT JOIN t_branches b on p.branch_id=b.id
	WHERE p.created_at BETWEEN ? and ?
		GROUP BY b.name, date(created_at) order by d
	";
	return DB::select($sql,[$from_date,$to_date.' 23:59:59']);
	}



	public function result_period_payments_all($from,$till)
	{
	$sql = "
	SELECT
		SUM(p.cash) as cash,
		SUM(p.plastic_card) as plastic_card,
		SUM(p.bank_account) as bank_account,
		SUM(p.payme) as payme,
		SUM(p.click) as click,
		SUM(p.cheque_sum) as cheque_sum,
		SUM(p.online_payment) as online_payment,
		SUM(p.humo) as humo,
		SUM(p.books) as books,
		SUM(p.balans) as balans,
		SUM(p.total_payment) as total_payment,
		b.name
	FROM ".DB::getTablePrefix()."payments p
	LEFT JOIN t_branches b on p.branch_id=b.id
	WHERE p.created_at BETWEEN ? and ?
		GROUP BY b.name
	";
	return DB::select($sql,[$from,$till.' 23:59:59']);
	}
	public function result_dayly_payments_branch($date,$branch_id){
		// echo $tags;
		$sql = "
			SELECT
				SUM(p.cash) as cash,
				SUM(p.plastic_card) as plastic_card,
				SUM(p.bank_account) as bank_account,
				SUM(p.payme) as payme,
				SUM(p.click) as click,
				SUM(p.cheque_sum) as cheque_sum,
				SUM(p.online_payment) as online_payment,
				SUM(p.humo) as humo,
				SUM(p.books) as books,
				SUM(p.balans) as balans,
				SUM(p.total_payment) as total,
				b.name
			FROM ".DB::getTablePrefix()."payments p
			LEFT JOIN t_branches b on p.branch_id=b.id
			WHERE DATE(p.created_at) = ?
				AND branch_id = ?
				GROUP BY b.name
		";


		return DB::select($sql,[$date,$branch_id]);
	}




	public function get_period_total_cheque_branch($from,$till,$branch_id){
		$sql = "
				SELECT
					SUM(p.total_payment) as total,
					b.name
				FROM ".DB::getTablePrefix()."payments p
				LEFT JOIN t_branches b on p.branch_id=b.id
				WHERE p.created_at BETWEEN ? and ?
					AND branch_id = ? AND p.cheque=1
					GROUP BY b.name
		";

		return DB::select($sql,[$from,$till.' 23:59:59',$branch_id]);
	}
	public function result_period_payments_branch($from,$till,$branch_id){
		// echo $tags;
		$sql = "
			SELECT
				SUM(p.cash) as cash,
				SUM(p.plastic_card) as plastic_card,
				SUM(p.bank_account) as bank_account,
				SUM(p.payme) as payme,
				SUM(p.click) as click,
				SUM(p.cheque_sum) as cheque_sum,
				SUM(p.online_payment) as online_payment,
				SUM(p.humo) as humo,
				SUM(p.books) as books,
				SUM(p.balans) as balans,
				SUM(p.total_payment) as total,
				b.name
			FROM ".DB::getTablePrefix()."payments p
			LEFT JOIN t_branches b on p.branch_id=b.id
			WHERE p.created_at BETWEEN ? and ?
				AND branch_id = ?
				GROUP BY b.name
		";
		return DB::select($sql,[$from,$till.' 23:59:59',$branch_id]);
	}
	public function ecreator()
    {
        // return $this->belongsTo('App\Models\SmsReport','sms_report_id','id')->withDefault();
        return $this->belongsTo('App\Models\User','created_by','id');
    }
}
