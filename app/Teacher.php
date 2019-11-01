<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = ['id','teacher_type','dedication','home_institute','country'];
    public $timestamps = false;

    public function user() {
        return $this->belongsTo('App\User');
    }

    public static function getTeacherById($id)
    {
        return self::where('id',$id)
            ->get();
    }

    public static function existTeacherById($id)
    {
        return self::where('id',$id)
            ->exists();
    }

    public static function addTeacher($teacher)
    {
        self::create($teacher);
    }

    public static function updateTeacher($userId,$teacher)
    {
        self::where('id',$userId)
            ->get()[0]
            ->update($teacher);
    }
}
