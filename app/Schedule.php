<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    //
    protected $fillable = ['school_period_subject_teacher_id','day','classroom','start_hour','end_hour'];
    protected $table = 'schedules';
    public $timestamps = false;

    public static function addSchedule($schedule)
    {
        self::create($schedule);
    }
}
