<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    public $timestamps = false;

    protected $fillable = ['id','name','faculty_id','organization_id'];

    protected $keyType='string';

    public static function existOrganization($organizationId)
    {
        return self::where('id',$organizationId)
            ->exists();
    }
}
