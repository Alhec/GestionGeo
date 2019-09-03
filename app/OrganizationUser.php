<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrganizationUser extends Model
{
    public $timestamps = false;
    protected $table = 'organization_user';
    protected $fillable = ['user_id','organization_id'];

    public static function addOrganizationUser($organizationUser)
    {
        self::create($organizationUser);
    }

    public static function existOrganizationUser($userId)
    {
        return self::where('user_id',$userId)
            ->exists();
    }
}
