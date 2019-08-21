<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchoolPeriodStudent extends Model
{
    protected $table = 'school_period_student';
    public $timestamps = false;

    public function schoolPeriod()
    {
        return $this->belongsTo('App\SchoolPeriod');
    }
}
