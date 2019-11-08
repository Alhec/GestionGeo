<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Equivalence extends Model
{
    public $timestamps = false;

    protected $fillable = ['student_id','subject_id','qualification'];

    public static function addEquivalence($equivalence)
    {
        self::create($equivalence);
        try{

        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function deleteEquivalence($studentId)
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
