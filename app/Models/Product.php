<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class Product extends Model
{
	use Userstamps;

    protected $fillable = [
		'name', 'price', 'description',
	];

	public function sales(){
		return $this->hasMany('App\Models\Sales');
	}
}
