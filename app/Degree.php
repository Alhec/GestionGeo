<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Self_;

class Degree extends Model
{
    protected $fillable = ['student_id','degree_obtained','degree_name','degree_description','university'];

    protected $hidden = ['student_id'];

    public $timestamps = false;

    protected $primaryKey = 'student_id';

    public static function addDegree($degree)
    {
        try{
            self::create($degree);
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function deleteDegree($studentId)
    {
        try{
            self::where('student_id',$studentId)
                ->delete();
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }
}
