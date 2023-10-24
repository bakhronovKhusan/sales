<?php

namespace App\Models;

use App\Models\Company;
use App\Models\StaffSalary;
use Illuminate\Database\Eloquent\Model;

class SalaryType extends Model{

    protected $table = 'salary_types';

    protected $primaryKey = 'id';

    protected $fillable = ['name', 'company_id'];

    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function salaries(){
        return $this->hasMany(StaffSalary::class,'type_id','id');
    }

}
