<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Equivalence extends Model
{
    public $timestamps = false;

    protected $fillable = ['student_id','subject_id','qualification'];

    protected $hidden = ['student_id'];

    public function subject() {
        return $this->belongsTo('App\Subject');
    }

    public static function addEquivalence($equivalence)
    {
        try{
            self::create($equivalence);
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
