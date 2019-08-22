<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdministratorOrganization extends Model
{
    public $timestamps = false;
    protected $table = 'administrator_organization';
    protected $fillable = ['administrator_id','organization_id'];
}
