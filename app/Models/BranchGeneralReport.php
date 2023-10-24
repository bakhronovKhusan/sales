<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchGeneralReport extends Model{

    protected $fillable = [
    	'staff_id', 'active_students', 'finished_students', 'new_groups', 'individual', 'active_groups', 'closed_groups', 'period', 'branch_id',
	];

	public function staff(){
		return $this->belongsTo('App\Models\Staff');
	}

	public function branch(){
		return $this->belongsTo('App\Models\Branch');
	}
}
