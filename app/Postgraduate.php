<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Organization;

class Postgraduate extends Model
{
    //
    protected $fillable = ['postgraduate_name','num_cu'];
    protected $hidden = ['organization_id'];
    public $timestamps = false;

    /*Busca los postgrados asociados a una organizacion*/
    public static function getPostgraduates(String $organizationId)
    {
        $postgraduates = Postgraduate::where('organization_id',$organizationId)->get();
        if (count($postgraduates)>0){
            return $postgraduates;
        }
        return null;
    }

    /*Busca el postgrado con un id en una organizacion*/
    public static function getPostgraduateById(String $id,String $organization_id)
    {
        $postgraduate = Postgraduate::where('id',$id)
            ->where('organization_id',$organization_id);
        if ($postgraduate->exists()){
            return $postgraduate->get();
        }
        return null;
    }

    /*Buscar si existe la organizacion*/
    public static function existOrganization(String $organization_id)
    {
        $organization = Organization::find($organization_id);
        if ($organization!=null){
            return true;
        }
        return false;
    }

    /*Buscar si existe postgrado por nombre en una organzacion*/
    public static function existPostgraduate(String $name, String $organization_id)
    {
            return Postgraduate::where('postgraduate_name',$name)
            ->where('organization_id',$organization_id)
                ->exists();
    }

    /*Agregar registro en base de datos*/
    public static function addPostgraduate($postgraduate)
    {
        Postgraduate::create($postgraduate->all());
    }

    /*Buscar postgrado por nombre en una organzacion*/
    public static function findPostgraduate(String $name, String $organization_id)
    {
        $postgraduate = Postgraduate::where('postgraduate_name',$name)
            ->where('organization_id',$organization_id);
        if ($postgraduate->exists()){
            return $postgraduate->get()[0];
        }
        return null;
    }

    /* Verificar si existe un postgrado por id en una organizacion*/
    public static function existPostgraduateById(String $id, String $organization_id)
    {
        return Postgraduate::where('id',$id)
            ->where('organization_id',$organization_id)
                ->exists();
    }

    /*eliminar un elemento dado un id y una organizacion*/
    public static function deletePostgraduate($id)
    {
        Postgraduate::find($id)->delete();
    }

    /*Actualizar un postgrado en una oraganizacion*/
    public static function updatePotgraduate($id,$postgraduate)
    {
        Postgraduate::find($id)->update($postgraduate->all());
    }
}

