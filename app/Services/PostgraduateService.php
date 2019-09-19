<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Postgraduate;
use App\Organization;

class PostgraduateService
{

    public static function getPostgraduates(Request $request)
    {
        //self::sendEmail();
        $organizationId = $request->header('organization_key');
        $postgraduates = Postgraduate::getPostgraduates($organizationId);
        if (count($postgraduates)>0){
            return $postgraduates;
        }
        return response()->json(['message'=>'No existen postgrados'],206);
    }

    public static function getPostgraduatesById(Request $request, $id)
    {
        $organizationId = $request->header('organization_key');
        $postgraduate = Postgraduate::getPostgraduateById($id,$organizationId);
        if (count($postgraduate)>0) {
            return $postgraduate[0];
        }
        return response()->json(['message'=>'Postgrado no encontrado'],206);
    }

    public static function validate(Request $request)
    {
        $request->validate([
            'postgraduate_name'=>'required|max:100',
            'num_cu'=>'required|numeric',
        ]);
    }

    public static function addPostgraduate(Request $request)
    {
        self::validate($request);
        $organizationId = $request->header('organization_key');
        if (Organization::existOrganization($organizationId)){
            if (!Postgraduate::existPostgraduateByName($request['postgraduate_name'],$organizationId)){
                $request['organization_id']=$organizationId;
                $id = Postgraduate::addPostgraduate($request);
                return self::getPostgraduatesById($request,$id);
            }
            return response()->json(['message'=>'Nombre de postgrado en uso'],206);
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
        if (Organization::existOrganization($organizationId)){
            if (Postgraduate::existPostgraduateById($id,$organizationId)){
                $request['organization_id']=$organizationId;
                $postgraduateName=Postgraduate::getPostgraduateByName($request['postgraduate_name'],$organizationId);
                if (count($postgraduateName)>0){
                    if ($postgraduateName[0]['id']!=$id){
                        return response()->json(['message'=>'Nombre de postgrado en uso'],206);
                    }
                }
                Postgraduate::updatePostgraduate($id,$request);
                return self::getPostgraduatesById($request,$id);
            }
            return response()->json(['message'=>'Postgrado no encontrado'],206);
        }
        return response()->json(['message'=>'No existe organizacion asociada'],206);
    }
}
