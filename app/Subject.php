<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Subject extends Model
{
    protected $fillable = ['subject_code','subject_name','uc','subject_type'];

    public $timestamps = false;

    public function Postgraduates()
    {
        return $this->belongsToMany('App\Postgraduate','postgraduate_subject')
            ->as('postgraduateSubject')
            ->withPivot('type');
    }

    public static function getSubjects($organizationId){
        return self::with('postgraduates')
            ->whereHas('postgraduates',function (Builder $query) use ($organizationId){
                $query
                    ->where('organization_id','=',$organizationId);
            })
            ->get();
    }

    public static function getSubjectById($id,$organizationId){
        return self::where('id',$id)
            ->with('postgraduates')
            ->whereHas('postgraduates',function (Builder $query) use ($organizationId){
                $query
                    ->where('organization_id','=',$organizationId);
            })
            ->get();
    }

    public static function existSubjectByCode($code,$organizationId){
        return self::where('subject_code',$code)
            ->with('postgraduates')
            ->whereHas('postgraduates',function (Builder $query) use ($organizationId){
                $query
                    ->where('organization_id','=',$organizationId);
            })
            ->exists();
    }

    public static function addSubject($subject)
    {
        self::create($subject->all());
    }

    public static function getSubjectByParameters($code,$name,$uc)
    {
        return self::where('subject_code',$code)
            ->where('subject_name',$name)
            ->where('uc',$uc)
            ->get();
    }

    public static function getSubjectByCode($code,$organizationId)
    {
        return self::where('subject_code',$code)
            ->with('postgraduates')
            ->whereHas('postgraduates',function (Builder $query) use ($organizationId){
                $query
                    ->where('organization_id','=',$organizationId);
            })
            ->get();

    }

    public static function existSubjectById($id,$organizationId)
    {
        return self::where('id',$id)
            ->with('postgraduates')
            ->whereHas('postgraduates',function (Builder $query) use ($organizationId){
                $query
                    ->where('organization_id','=',$organizationId);
            })
            ->exists();
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
