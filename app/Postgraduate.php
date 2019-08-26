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

    /*Busca los postgrados asociados a una organizacion*/
    public static function getPostgraduates($organizationId)
    {
        $postgraduates = self::where('organization_id',$organizationId)->get();
        if (count($postgraduates)>0){
            return $postgraduates;
        }
        return null;
    }

    /*Busca el postgrado con un id en una organizacion*/
    public static function getPostgraduateById($id, $organization_id)
    {
        $postgraduate = self::where('id',$id)
            ->where('organization_id',$organization_id);
        if ($postgraduate->exists()){
            return $postgraduate->get()[0];
        }
        return null;
    }

    /*Buscar si existe la organizacion*/
    public static function existOrganization($organization_id)
    {
        $organization = Organization::find($organization_id);
        if ($organization!=null){
            return true;
        }
        return false;
    }

    /*Buscar si existe postgrado por nombre en una organzacion*/
    public static function existPostgraduate($name, $organization_id)
    {
            return self::where('postgraduate_name',$name)
            ->where('organization_id',$organization_id)
                ->exists();
    }

    /*Agregar registro en base de datos*/
    public static function addPostgraduate($postgraduate)
    {
        self::create($postgraduate->all());
    }

    /*Buscar postgrado por nombre en una organzacion*/
    public static function findPostgraduate($name, $organizationId)
    {
        $postgraduate = self::where('postgraduate_name',$name)
            ->where('organization_id',$organizationId);
        if ($postgraduate->exists()){
            return $postgraduate->get()[0];
        }
        return null;
    }

    /* Verificar si existe un postgrado por id en una organizacion*/
    public static function existPostgraduateById($id, $organizationId)
    {
        return self::where('id',$id)
            ->where('organization_id',$organizationId)
                ->exists();
    }

    /*eliminar un elemento dado un id y una organizacion*/
    public static function deletePostgraduate($id)
    {
        self::find($id)->delete();
    }

    /*Actualizar un postgrado en una oraganizacion*/
    public static function updatePotgraduate($id,$postgraduate)
    {
        self::find($id)->update($postgraduate->all());
    }

    /*obtener relacion con organizacion*/
    public function organization()
    {
        return $this->belongsTo('App\Organization');
    }

}

