<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['subject_code','subject_name','uc','subject_type'];

    public function Postgraduates(){
        return $this->belongsToMany('App\Postgraduate','postgraduate_subject')->withPivot('postgraduate_id','type');
    }
}
