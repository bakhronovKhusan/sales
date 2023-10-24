<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class Room extends Model{
	use Userstamps;

	protected $fillable = [
		'name', 'branch_id',
	];

	public function groups(){
		return $this->belongsToMany('App\Models\Group','schedule');
	}

	public function branch(){
		return $this->belongsTo('App\Models\Branch')->withDefault();
	}
}
