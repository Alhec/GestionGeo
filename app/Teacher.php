<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Teacher extends Model
{
    protected $fillable = ['id','teacher_type','dedication','category','home_institute','country'];
    public $timestamps = false;

    public function user() {
        return $this->belongsTo('App\User','id','id');
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

    public static function deleteTeacher($id)
    {
        try{
            self::where('id',$id)
                ->delete();
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }
}
