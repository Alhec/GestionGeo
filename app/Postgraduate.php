<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Postgraduate extends Model
{
    //
    protected $fillable = ['postgraduate_name','num_cu'];

    public function  subjects(){
        return $this->belongsToMany('App\Subject','postgraduate_subject')->withPivot('subject_id','type');
    }
}
