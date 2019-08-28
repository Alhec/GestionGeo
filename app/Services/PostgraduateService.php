<?php

namespace App\Services;


use Illuminate\Http\Request;
use App\Postgraduate;

class PostgraduateService
{

    public static function getPostgraduates(Request $request)
    {
        $organizationId = $request->header('organization_key');
        $postgraduates = Postgraduate::getPostgraduates($organizationId);
        if ($postgraduates != null){
            return $postgraduates;
        }
        return response()->json(['message'=>'No existen postgrados'],206);
    }

    public static function getPostgraduatesById(Request $request, $id)
    {
        $organizationId = $request->header('organization_key');
        $postgraduate = Postgraduate::getPostgraduateById($id,$organizationId);
        if ($postgraduate!=null) {
            return $postgraduate;
        }
       return response()->json(['message'=>'Postgrado no encontrado'],206);
    }

    public static function validate(Request $request)
    {
        $request->validate([
            'postgraduate_name'=>'required|max:50',
            'num_cu'=>'required|numeric',
        ]);
    }

    public static function addPostgraduate(Request $request)
    {
        self::validate($request);
        $organizationId = $request->header('organization_key');
        if (Postgraduate::existOrganization($organizationId)){//preguntar si laorganizacion asociada existe
            if (!Postgraduate::existPostgraduate($request['postgraduate_name'],$organizationId)){
                $request['organization_id']=$organizationId;
                Postgraduate::addPostgraduate($request);
                return Postgraduate::findPostgraduate($request['postgraduate_name'],$organizationId);
            }
            return response()->json(['message'=>'Postgrado ya registrado'],206);
        }
        return response()->json(['message'=>'No existe organizacion asociada'],206);
    }

    public static function deletePostgraduate(Request $request, $id)
    {
        $organizationId = $request->header('organization_key');
        if (Postgraduate::existPostgraduateById($id,$organizationId)){
            Postgraduate::deletePostgraduate($id);
            return response()->json(['message'=>'OK']);
        }
        return response()->json(['message'=>'Postgrado no encontrado'],206);
    }

    public static function updatePostgraduate(Request $request, $id)
    {
        self::validate($request);
        $organizationId = $request->header('organization_key');
        if (Postgraduate::existPostgraduateById($id,$organizationId)){
            $request['organization_id']=$organizationId;
            $postgraduateName=Postgraduate::findPostgraduate($request['postgraduate_name'],$organizationId);
            if ($postgraduateName!=null){
                if ($postgraduateName['id']==$id){
                    Postgraduate::updatePotgraduate($id,$request);
                    return PostgraduateService::getPostgraduatesById($request,$id);
                }
                return response()->json(['message'=>'nombre de Postgrado en uso'],206);
            }
            Postgraduate::updatePotgraduate($id,$request);
            return PostgraduateService::getPostgraduatesById($request,$id);
        }
        return response()->json(['message'=>'Postgrado no encontrado'],206);
    }

}