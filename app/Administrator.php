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
            return User::where('organization_id',$organizationId)
                ->whereHas('roles',function (Builder $query){
                    $query
                        ->where('user_type','=','A');
                })
                ->whereHas('administrator',function (Builder $query){
                    $query
                        ->where('rol','=','COORDINATOR')
                        ->where('principal','=',1);
                })
                ->with('administrator')
                ->with('teacher')
                ->with('student')
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function deleteAdministrator($id)
    {
        try{
            self::where('id',$id)
                ->delete();
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }
}
