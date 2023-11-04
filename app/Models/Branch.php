<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class Branch extends Model{
	use Userstamps;
    protected $table = 'branches';
	protected $fillable = [
		'name', 'phone', 'address', 'location', 'website', 'email', 'facebook', 'telegram', 'instagram', 'youtube', 'bank_name', 'bank_account', 'bank_code', 'inn', 'company_id', 'max_times'
	];

	public function students(){
		return $this->hasMany('App\Models\Student');
	}

	public function groups(){
		return $this->hasMany('App\Models\Group');
	}

	// public function staff(){
	// 	return $this->hasMany('App\Models\ModelsStaff');
	// }

	public function payments(){
		return $this->hasMany('App\Models\Payment');
	}

	public function rooms(){
		return $this->hasMany('App\Models\Room');
	}

	public function salaries(){
		return $this->hasMany('App\Models\Salary');
	}

	public function sales(){
		return $this->hasMany('App\Models\Sales');
	}

	public function staff(){
		return $this->belongsToMany('App\Models\Staff','branch_staff','branch_id','staff_id')->withPivot('main');
	}

	public function company(){
		return $this->belongsTo('App\Models\Company');
	}

	public function getConversionAttribute()
	{
		$users = \App\User::role(['Administrator','Manager','Senior Manager'])
            ->whereHas('staff',function($q){
                $q->whereHas('branches',function($qq){
                    $qq->where('id',$this->id);
                });
            })
            ->where(['type'=>'staff'])
            ->get();
        $t=array();
        $date=date("Y-m-01 00:00:00");
        $atest=0;$came=0;
        foreach($users as $key => $user){
            $atest+=Track::where(['staff_id'=>$user['type_id'],'status'=>'atested'])
            ->where('created_at','>=',$date)
            ->count();
            $came+=Track::where(['staff_id'=>$user['type_id'],'status'=>'cametotrial'])
            ->where('created_at','>=',$date)
            ->count();
        }
        if ($atest!=0)
        return number_format($came/$atest*100,2,"."," ");
        else return 0;

	}
}
