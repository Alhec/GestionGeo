<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Advisor extends Model
{
    public $timestamps = false;
    protected $fillable = ['final_work_id','teacher_id'];

    public static function addAdvisor($advisor)
    {
        try{
            self::create($advisor);
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function deleteAllAdvisor($finalWorkId)
    {
        try{
            self::where('final_work_id',$finalWorkId)
                ->delete();
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }

    }
}
