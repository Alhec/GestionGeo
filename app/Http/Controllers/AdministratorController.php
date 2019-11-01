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
        return UserService::getUsers($request,'A');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /*public function store(Request $request)
    {
        return UserService::addUser($request,'A');
    }*/

    public function store(Request $request)
    {
        return AdministratorService::addAdministrator($request);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,Request $request)
    {
        return UserService::getUserById($request,$id,'A');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*public function update($id,Request $request)
    {
       return UserService::updateUser($request,$id,'A');
    }*/

    public function update($id,Request $request)
    {
        return AdministratorService::updateAdministrator($request,$id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*public function destroy($id, Request $request)
    {
        return UserService::deleteUser($request,$id,'A');
    }*/

    public function destroy($id, Request $request)
    {
        return AdministratorService::deleteAdministrator($request,$id);
    }

    public function active(Request $request)
    {
        return UserService::activeUsers($request,'A');
    }

    public function principal(Request $request)
    {
        return AdministratorService::getPrincipalCoordinator($request);
    }
}
