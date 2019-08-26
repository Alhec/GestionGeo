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

    public static function getPostgraduateSubject($subjectId)
    {
        return self::where('subject_id',$subjectId)->get();
    }

    public static function updatePostgraduateSubject($id,$postgraduateSubject)
    {
        self::find($id)->update($postgraduateSubject);
    }

    public static function findPostgraduateSubject($postgraduateId,$subjectId)
    {
        return self::where('postgraduate_id',$postgraduateId)
            ->where('subject_id',$subjectId)->get('id')[0];
    }

    public static function deletePostgraduateSubject($postgraduateSubjectId)
    {
        self::find($postgraduateSubjectId)->delete();
    }
}
