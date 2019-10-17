<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{

    protected $fillable = ['school_program_id','user_id','home_university','guide_teacher_id','student_type',
        'current_postgraduate','type_income','is_available_final_work?','is_ucv_teacher?','repeat_approved_subject?',
        'repeat_reprobated_subject?','credits_granted'];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function degrees()
    {
        return $this->hasMany('App\Degree');
    }

    public function guideTeacher()
    {
        return $this->hasOne('App\Teacher','id','guide_teacher_id')
            ->with('User');
    }

    public static function addStudent($student)
    {
        self::create($student);
    }

    public static function updateStudent($userId,$student)
    {
        self::where('user_id',$userId)
            ->get()[0]
            ->update($student);
    }

    public static function existStudentById($id)
    {
        return self::where('id',$id)
            ->exists();
    }

    public static function getStudentById($id)
    {
        return self::where('id',$id)
            ->get();
    }
}
