<?php

namespace App\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;

class Module extends Model{

	protected $fillable = [
		'name',
		'description'
	];

	public function companies(){
		return $this->belongsToMany(Company::class);
	}

}
