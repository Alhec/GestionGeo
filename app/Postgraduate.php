<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Organization;

class Postgraduate extends Model
{
    //
    protected $fillable = ['postgraduate_name','num_cu','organization_id'];

    protected $hidden = ['organization_id'];

    public $timestamps = false;

    public static function getPostgraduates($organizationId)
    {
        return self::where('organization_id',$organizationId)
            ->get();
    }

    public static function getPostgraduateById($id, $organization_id)
    {
        return self::where('id',$id)
            ->where('organization_id',$organization_id)
            ->get();
    }

    public static function existPostgraduateByName($name, $organizationId)
    {
        return self::where('postgraduate_name',$name)
            ->where('organization_id',$organizationId)
            ->exists();
    }

    public static function addPostgraduate($postgraduate)
    {
        self::create($postgraduate->all());
    }

    public static function getPostgraduateByName($name, $organizationId)
    {
        return self::where('postgraduate_name',$name)
            ->where('organization_id',$organizationId)
            ->get();
    }

    public static function existPostgraduateById($id, $organizationId)
    {
        return self::where('id',$id)
            ->where('organization_id',$organizationId)
                ->exists();
    }

    public static function deletePostgraduate($id)
    {
        self::find($id)
            ->delete();
    }

    public static function updatePostgraduate($id,$postgraduate)
    {
        self::find($id)
            ->update($postgraduate->all());
    }

    public function organization()
    {
        return $this->belongsTo('App\Organization');
    }

}

