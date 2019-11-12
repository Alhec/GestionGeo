<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Administrator extends Model
{
    public $timestamps = false;

    protected $fillable = ['id','rol','principal'];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public static function getAdministratorById($id)
    {
        try{
            return self::where('id',$id)
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function existAdministratorById($id)
    {
        try{
            return self::where('id',$id)
                ->exists();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function addAdministrator($administrator)
    {
        try{
            self::create($administrator);
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }

    }

    public static function updateAdministrator($userId,$administrator)
    {
        try{
            self::where('id',$userId)
                ->get()[0]
                ->update($administrator);
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }



    public static function getPrincipalCoordinator($organizationId)
    {
        try{
            return User::where('user_type','A')
                ->where('organization_id',$organizationId)
                ->whereHas('administrator',function (Builder $query){
                    $query
                        ->where('rol','=','COORDINATOR')
                        ->where('principal','=',1);
                })
                ->with('administrator')
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }
}
