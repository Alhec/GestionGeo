<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostgraduateSubject extends Model
{
    protected $table = 'postgraduate_subject';

    protected $fillable = ['postgraduate_id','subject_id','type'];

    public $timestamps = false;

    public static function addPostgraduateSubject($postgraduateSubject)
    {
        self::create($postgraduateSubject);
    }

    public static function getPostgraduateSubjectBySubjectId($subjectId)
    {
        return self::where('subject_id',$subjectId)
            ->get();
    }

    public static function updatePostgraduateSubject($id,$postgraduateSubject)
    {
        self::find($id)
            ->update($postgraduateSubject);
    }

    public static function getPostgraduateSubject($postgraduateId,$subjectId)
    {
        return self::where('postgraduate_id',$postgraduateId)
            ->where('subject_id',$subjectId)
            ->get();
    }

    public static function deletePostgraduateSubject($id)
    {
        self::find($id)
            ->delete();
    }
}
