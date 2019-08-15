<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\User;

class AdministratorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $administrators= User::where('user_type','A')->get();
        return $administrators;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $administrator = User::where(['identification'=>$request['identification']])->orWhere(['email'=>$request['email']])->get();
      //return count($administrator);
        if (count($administrator)>0){//valida que el administrador no exista
            return response()->json(['message'=>'Identificacion o Correo ya registrados'],206);
        }else{
            $request['user_type']='A';
            User::create($request->all());
            return response()->json(['message'=>'OK']);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
            $administrator = User::where(['id'=>$id])->where(['user_type'=>'A'])->get();
            if (count($administrator)>0){//Valida si existe el registro
                return response()->json($administrator[0]);
            }else{
                return response()->json(['message'=>'Administrador no encontrado'],206);
            }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $administrator = User::where(['id'=>$id])->where(['user_type'=>'A'])->get();
        if (count($administrator)>0){ // valida que el administrador exista para actualizarlo
            $userEmail =  User::where(['email'=>$request['email']])->get();
            $userIdentification =  User::where(['identification'=>$request['identification']])->get();
            if (count($userEmail)>0 ){ // valida si existe un correo ya suministrado en bd igual al enviado en la peticion
                if ($userEmail[0]['id'] == $administrator[0]['id']){ // si son diferentes el correo ya esta ocupado por otro usuario
                    if (count($userIdentification)>0){ // valida si ya existe un identificador como ci o rif en bd
                        if ($userIdentification[0]['id'] == $administrator[0]['id']){ // si son diferentes la ci o rif la tiene otro usuario
                            $request['user_type']='A';
                            $administrator[0]->update($request->all());
                            return response()->json(['message'=>'OK']);
                        }else{
                            return response()->json(['message'=>'Identificacion registrada'],206);
                        }
                    }else{// el caso en que esten disponible la ci o rif
                        $request['user_type']='A';
                        $administrator[0]->update($request->all());
                        return response()->json(['message'=>'OK']);
                    }
                }else{
                    return response()->json(['message'=>'Correo ya registrado'],206);
                }
            }else{
                if (count($userIdentification)>0){
                    if ($userIdentification[0]['id'] == $administrator[0]['id']){
                        $request['user_type']='A';
                        $administrator[0]->update($request->all());
                        return response()->json(['message'=>'OK']);
                    }else{
                        return response()->json(['message'=>'Identificacion registrada'],206);
                    }
                }else{
                    $request['user_type']='A';
                    $administrator[0]->update($request->all());
                    return response()->json(['message'=>'OK']);
                }
            }
        }else{
            return response()->json(['message'=>'Administrador no encontrado'],206);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $administrator = User::where(['id'=>$id])->where(['user_type'=>'A'])->get();
        if (count($administrator)>0){ //valida que el administrador exista par eliminarlo
            $administrator[0]->delete();
            return response()->json(['message'=>'OK'],204);
        }else{
            return response()->json(['message'=>'Administrador no encontrado'],206);
        }

        //User::where(['id'=>$id,'user_type'=>'A'])->get()[0]->delete();
        //return response()->json(['message'=>'OK']);
    }
}
