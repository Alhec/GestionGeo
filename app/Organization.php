<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Organization extends Model
{
    public $timestamps = false;

    protected $fillable = ['id','name','faculty_id','organization_id','website','address'];

    protected $keyType='string';

    public function users()
    {
        return $this->hasMany('App\User','organization_id','id');
    }

    public static function getOrganizationById($organizationId)
    {
        try{
            return self::where('id',$organizationId)
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function getOrganizations(){
        try{
            return self::get('id');
        }catch (\Exception $e){
            return 0;
        }
    }
}
