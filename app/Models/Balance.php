<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class Balance extends Model
{
    use Userstamps;

    // Disable default updated at timestamp
    const UPDATED_AT = null;

    protected $table = 'balance';

    protected $primaryKey = 'id';

    protected $fillable = [
        'student_id',
        'debit',
        'credit',
        'teacher_id',
        'group_id',
        'level_id',
        'branch_id',
        'is_recalculated',
        'details'
    ];

    protected $casts = [
        'details' => 'array',
    ];

    // set default updated_by userstamp null
    public function setUpdatedByAttribute($value) {
        return null;
    }

    public function student(){
        return $this->belongsTo(Student::class);
    }
}
