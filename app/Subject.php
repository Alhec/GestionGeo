<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['subject_code','subject_name','uc','subject_type'];

    public $timestamps = false;

    public function Postgraduates()
    {
        return $this->belongsToMany('App\Postgraduate','postgraduate_subject')
            ->as('postgraduateSubject')
            ->with('organization')
            ->withPivot('type');
    }

    public static function getSubjects(){
        return self::with('postgraduates')
            ->get();
    }

    public static function getSubjectById($id){
        return self::with('postgraduates')
            ->find($id);
    }

    public static function existSubject($code){
        return self::where('subject_code',$code)
            ->exists();
    }

    public static function addSubject($subject)
    {
        self::create($subject->all());
    }

    public static function findSubject($code)
    {
        $subject =self::where('subject_code',$code);
        if ($subject->exists()){
            return $subject->get()[0];
        }
        return null;
    }

    public static function existSubjectById($id)
    {
        return self::find($id);
    }

    public static function deleteSubject($id)
    {
        self::find($id)
            ->delete();
    }

    public static function updateSubject($id,$subject)
    {
        self::find($id)
            ->update($subject->all());
    }
}
