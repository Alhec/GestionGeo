<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FinalWork extends Model
{
    public $timestamps = false;
    protected $table = 'final_work';

    public static function addFinalWork($finalWork){
        try{
            return self::create($finalWork);
        }catch (\Exception $e){
            return 0;
        }
    }
}
