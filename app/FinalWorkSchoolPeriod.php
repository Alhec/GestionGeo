<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FinalWorkSchoolPeriod extends Model
{
    public $timestamps = false;

    protected $table = 'final_work_school_period';

    protected $fillable = ['status','description_status','final_work_id','school_period_student_id'];

    public function finalWork()
    {
        return $this->belongsTo('App\FinalWork');
    }

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

    public static function existFinalWorkSchoolPeriodBySchoolPeriodStudent($schoolPeriodStudentId){
        try{
            return self::where('school_period_student_id',$schoolPeriodStudentId)
                ->exists();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function deleteFinalWorkSchoolPeriodBySchoolPeriodStudentId($schoolPeriodStudentId){
        try{
            return self::where('school_period_student_id',$schoolPeriodStudentId)
                ->delete();
        }catch (\Exception $e){
            return 0;
        }
    }
}
