<?php

namespace App\Services;


use Illuminate\Http\Request;
use App\Postgraduate;

class PostgraduateService
{

    public static function getPostgrduates(Request $request)
    {
        $organization_id = $request->header('organization_key');
        $postgraduates = Postgraduate::getPostgraduates($organization_id);
        if ($postgraduates != null){
            return $postgraduates;
        }
        return response()->json(['message'=>'No existen postgrados'],206);
    }

    public static function getPostgraduatesById(Request $request, String $id)
    {
        $organization_id = $request->header('organization_key');
        $postgraduate = Postgraduate::getPostgraduateById($id,$organization_id);
        if ($postgraduate!=null) {
            return $postgraduate;
        }
       return response()->json(['message'=>'Postgrado no encontrado'],206);
    }

    public static function validate(Request $request)
    {
        $validated = $request->validate([
            'postgraduate_name'=>'required|max:50',
            'num_cu'=>'required|numeric',
        ]);
        return $validated;

    }

    public static function addPostgraduate(Request $request)
    {
        PostgraduateService::validate($request);
        $organization_id = $request->header('organization_key');
        if (Postgraduate::existOrganization($organization_id)){//preguntar si laorganizacion asociada existe
            if (!Postgraduate::existPostgraduate($request['postgraduate_name'],$organization_id)){
                $request['organization_id']=$organization_id;
                Postgraduate::addPostgraduate($request);
                return Postgraduate::findPostgraduate($request['postgraduate_name'],$organization_id);
            }
            return response()->json(['message'=>'Postgrado ya registrado'],206);
        }
        return response()->json(['message'=>'No existe organizacion asociada'],206);;
    }

    public static function deletePostgraduate(Request $request, String $id)
    {
        $organization_id = $request->header('organization_key');
        if (Postgraduate::existPostgraduateById($id,$organization_id)){
            Postgraduate::deletePostgraduate($id);
            return response()->json(['message'=>'OK']);
        }
        return response()->json(['message'=>'Postgrado no encontrado'],206);
    }

    public static function updatePostgraduate(Request $request, String $id)
    {
        PostgraduateService::validate($request);
        $organization_id = $request->header('organization_key');
        if (Postgraduate::existPostgraduateById($id,$organization_id)){
            $request['organization_id']=$organization_id;
            $postgraduateName=Postgraduate::findPostgraduate($request['postgraduate_name'],$organization_id);
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