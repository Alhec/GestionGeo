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
        return $this->hasMany('App\User','organization_id','id');
    }

    public static function getOrganization($organizationId)
    {
        try{
            return self::where('id',$organizationId)
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function getOrganizationByStudentId($userId)
    {
        try{
            return self::whereHas('users',function (Builder $query) use ($userId){
                $query
                    ->where('id','=',$userId);
            })
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }
}
