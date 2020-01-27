<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FinalWorkSchoolPeriod extends Model
{
    public $timestamps = false;
    protected $table = 'final_work_school_period';
    protected $fillable = ['id','final_work_id','school_program_id','status','description_status'];

    public static function addFinalWorkSchoolPeriod($finalWorkSchoolPeriod)
    {
        try{
            return self::create($finalWorkSchoolPeriod);
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function updateFinalWorkSchoolPeriod($id,$finalWorkSchoolPeriod)
    {
        try{
            return self::find($id)
                ->update($finalWorkSchoolPeriod);
        }catch (\Exception $e){
            return 0;
        }
    }
}
