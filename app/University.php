<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class University extends Model
{
    protected $keyType='string';

    public $timestamps = false;

    protected $fillable = ['id','name','acronym'];

}
