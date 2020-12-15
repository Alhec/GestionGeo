<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    protected $fillable = ['user_type'];
    public $timestamps = false;

    public function user() {
        return $this->belongsTo('App\User','id','id');
    }
}
