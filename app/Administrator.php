<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Administrator extends Model
{
    public $timestamps = false;

    protected $fillable = ['id','rol','principal'];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public static function getAdministratorById($id)
    {
        return self::where('id',$id)
            ->get();
    }

    public static function existAdministratorById($id)
    {
        return self::where('id',$id)
            ->exists();
    }

    public static function addAdministrator($administrator)
    {
        self::create($administrator);
    }

    public static function updateAdministrator($userId,$administrator)
    {
        self::where('id',$userId)
            ->get()[0]
            ->update($administrator);
    }



    public static function getPrincipalCoordinator($organizationId)
    {
        $user = User::where('user_type','A')
            ->whereHas('organization',function (Builder $query) use ($organizationId){
                $query
                    ->where('id','=',$organizationId);
            })
            ->whereHas('administrator',function (Builder $query){
                $query
                    ->where('rol','=','COORDINATOR')
                    ->where('principal','=',1);
            });
        return $user
            ->with('administrator')
            ->get();
    }
}
