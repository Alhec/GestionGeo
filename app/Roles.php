<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    protected $fillable = ['user_id','user_type'];
    public $timestamps = false;

    public function user() {
        return $this->belongsTo('App\User','id','user_id');
    }

    public static function addRol($rol)
    {
        try{
            self::create($rol);
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function deleteRol($userId,$userType)
    {
        try{
            self::where('user_id',$userId)
                ->where('user_type',$userType)
                ->delete();
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }

    }
}
