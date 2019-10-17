<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Degree extends Model
{
    protected $fillable = ['student_id','degree_obtained','degree_name','degree_description','university'];

    public $timestamps = false;


}
