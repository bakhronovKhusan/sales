<?php

namespace App\Models;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Wildside\Userstamps\Userstamps;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable implements JWTSubject {

    use Notifiable, Userstamps, HasRoles;
    protected $guard_name = 'api';

    const LANG_UZB = 1;
    const LANG_RUS = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'type_id',
        'phone',
        'name',
        'email',
        'password',
        'lang'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function student(){
        return $this->belongsTo('App\Models\Student','type_id','id')->withDefault();
    }

    public function staff(){
        return $this->belongsTo('App\Models\Staff','type_id','id')->withDefault();
    }

    public function tracks(){
        return $this->hasMany('App\Models\Track','created_by','id');
    }

    public function getAllPermissionsAttribute() {
        $permissions = [];
        foreach (Permission::all() as $permission) {
            if (Auth::user()->can($permission->name)) {
                $permissions[] = $permission->name;
            }
        }
        return $permissions;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
