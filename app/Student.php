<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{

    protected $fillable = ['postgraduate_id','user_id','home_university','student_type','current_postgraduate','degrees'];

    public $timestamps = false;

    public function user() {
        return $this->belongsTo('App\User');
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

    public static function existStudentByid($id)
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
