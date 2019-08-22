<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organiation extends Model
{
    public $timestamps = false;

    protected $fillable = ['id','name','faculty_id'];
}
