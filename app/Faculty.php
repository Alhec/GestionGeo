<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    public $timestamps = false;
    protected $keyType='string';
    protected $fillable = ['id','name','university_id'];
}
