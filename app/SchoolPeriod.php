<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchoolPeriod extends Model
{
    protected $fillable = ['cod_school_period','start_date','end_date'];
}
