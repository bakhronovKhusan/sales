<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;

// use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class GroupStudent extends Model
{
    use Userstamps;

    // protected $primaryKey = null;
    protected $table = 't_group_student';

    protected $primaryKey = ['group_id', 'student_id'];

    public $incrementing = false;

    protected $fillable = [
        'group_id',
        'student_id',
        'status',
        'balance',
        'lessons_left',
        'missed_lessons',
        'missed_trials',
        'np_days_count',
        'called',
        'called_count',
        'debtors_sms_count',
        'comment',
        'exception_sum',
        'exception_sum_expire_date',
        'exception_sum_reason',
        'exception_by',
        'exception_accepted_by',
        'exception_status',
        'exception_cancel',
        'exception_change_time',
        'exception_created_by',
        'exception_created_at',
        'archived_at',
        'comment_failed',
        'administrator_id',
        'activated_at',
        'old_activated_at',
        'started_at',
    ];

    protected $appends = ['days_from_last_update'];

    //Set the keys for a save update query.
    protected function setKeysForSaveQuery($query)
    { //edit Builder $query to $query
        foreach ($this->getKeyName() as $key) {
            // UPDATE: Added isset() per devflow's comment.
            if (isset($this->$key)) {
                $query->where($key, '=', $this->$key);
            } else
                throw new Exception(__METHOD__ . 'Missing part of the primary key: ' . $key);
        }
        return $query;
    }


    protected function getKeyForSaveQuery($keyName = null)
    {
        if (is_null($keyName)) {
            $keyName = $this->getKeyName();
        }
        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }
        return $this->getAttribute($keyName);
    }


    //Execute a query for a single record by ID.
    public static function find($ids, $columns = ['*'])
    {
        $me = new self;
        $query = $me->newQuery();
        foreach ($me->getKeyName() as $key) {
            $query->where($key, '=', $ids[$key]);
        }
        return $query->first($columns);
    }

    public function getKey()
    {
        $attributes = [];
        foreach ($this->getKeyName() as $key) {
            $attributes[$key] = $this->getAttribute($key);
        }
        return $attributes;
    }

    public function group()
    {
        return $this->belongsTo('App\Models\Group')->withDefault();
    }

    public function ecreator()
    {
        // return $this->belongsTo('App\SmsReport','sms_report_id','id')->withDefault();
        return $this->belongsTo('App\Models\User', 'exception_created_by', 'id');
    }

    public function student()
    {
        return $this->belongsTo('App\Models\Student')->withDefault();
    }

    public function getDaysFromLastUpdateAttribute(): int
    {
        return $this->updated_at?->diff(now())?->days;
    }
}
