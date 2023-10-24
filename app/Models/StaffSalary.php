<?php

namespace App\Models;

use App\Models\SalaryType;
use Illuminate\Database\Eloquent\Model;

class StaffSalary extends Model{

    protected $table = 'staff_salary';

	protected $fillable = [
    	'staff_id', 'type_id', 'sum', 'is_active', 'date'
    ];

    public function staff(){
    	return $this->belongsTo('App\Models\Staff');
    }

    public function type(){
        return $this->belongsTo(SalaryType::class,'type_id','id');
    }
}
