<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostgraduateSubject extends Model
{
    protected $table = 'postgraduate_subject';
    protected $fillable = ['postgraduate_id','subject_id','type'];
    public $timestamps = false;
}
