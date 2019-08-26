<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrganizationUser extends Model
{
    public $timestamps = false;
    protected $table = 'organization_user';
    protected $fillable = ['user_id','organization_id'];
}
