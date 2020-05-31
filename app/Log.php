<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Log extends Model
{
    protected $fillable = ['user_id','log_description'];

    public static function addLog($userId,$logDescription)
    {
        try{
            return self::create([
                'user_id'=>$userId,
                'log_description'=>$logDescription
            ]);
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }
}
