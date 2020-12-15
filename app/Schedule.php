<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    //
    protected $fillable = ['school_period_subject_teacher_id','day','classroom','start_hour','end_hour'];
    protected $table = 'schedules';
    public $timestamps = false;
    protected $primaryKey = 'school_period_subject_teacher_id';

    public static function addSchedule($schedule)
    {
        try{
            self::create($schedule);
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function deleteAllSchedule($schoolPeriodSubjectTeacherId)
    {
        try{
            self::where('school_period_subject_teacher_id',$schoolPeriodSubjectTeacherId)
                ->delete();
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }

    }
}
