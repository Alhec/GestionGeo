<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Services\AdministratorService;

/**
 * @OA\Info(title="API Usuarios", version="1.0")
 *
 * @OA\Server(url="http://localhost:8000")
 */

class AdministratorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Mostrar usuarios",
     *     @OA\Response(
     *         response=200,
     *         description="Mostrar todos los usuarios."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function index(Request $request)
    {
        $organizationId = $request->header('Organization-Key');
        return UserService::getUsers($request,'A',$organizationId);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $organizationId = $request->header('Organization-Key');
        return AdministratorService::addAdministrator($request,$organizationId);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,Request $request)
    {
        $organizationId = $request->header('Organization-Key');
        return UserService::getUserById($request,$id,'A',$organizationId);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request)
    {
        $organizationId = $request->header('Organization-Key');
        return AdministratorService::updateAdministrator($request,$id,$organizationId);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $organizationId = $request->header('Organization-Key');
        return AdministratorService::deleteAdministrator($request,$id,$organizationId);
    }

    public function active(Request $request)
    {
        $organizationId = $request->header('Organization-Key');
        return UserService::activeUsers($request,'A',$organizationId);
    }

    public function principal(Request $request)
    {
        $organizationId = $request->header('Organization-Key');
        return AdministratorService::getPrincipalCoordinator($request,$organizationId,false);
    }
}
