<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class university extends Model
{
    protected $keyType='string';

    public $timestamps = false;

    protected $fillable = ['id','name','acronym'];

}
