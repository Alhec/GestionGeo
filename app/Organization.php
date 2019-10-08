<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Organization extends Model
{
    public $timestamps = false;

    protected $fillable = ['id','name','faculty_id','organization_id','website'];

    protected $keyType='string';

    public function users()
    {
        return $this->hasMany('App\OrganizationUser','organization_id','id');
    }

    public static function existOrganization($organizationId)
    {
        return self::where('id',$organizationId)
            ->exists();
    }
    public static function getOrganization($organizationId)
    {
        return self::where('id',$organizationId)
            ->get();
    }

    public static function getOrganizationByStudentId($userId)
    {
        return self::whereHas('users',function (Builder $query) use ($userId){
            $query
                ->where('user_id','=',$userId);
            })
            ->get();
    }
}
