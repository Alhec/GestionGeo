<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = ['id','teacher_type','dedication','home_institute','country'];
    public $timestamps = false;

    public function user() {
        return $this->belongsTo('App\User','id','id');
    }

    public static function getTeacherById($id)
    {
        try{
            return self::where('id',$id)
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function existTeacherById($id)
    {
        try{
            return self::where('id',$id)
                ->exists();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function addTeacher($teacher)
    {
        try{
            self::create($teacher);
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function updateTeacher($userId,$teacher)
    {
        try{
            self::where('id',$userId)
                ->get()[0]
                ->update($teacher);
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }
}
