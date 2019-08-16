<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Postgraduate;

class PostgraduateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $postgraduates = Postgraduate::all();
        if (count($postgraduates)>0) {
            return $postgraduates;
        }else{
            return response()->json(['message'=>'No existen postgrados'],206);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $postgraduate = Postgraduate::where(['postgraduate_name'=>$request['postgraduate_name']])->get();
        if (count($postgraduate)>0){
            return response()->json(['message'=>'Postgrado ya registrado'],206);
        }else{
            Postgraduate::create($request->all());
            $postgraduate = Postgraduate::where(['postgraduate_name'=>$request['postgraduate_name']])->get()[0];
            return $postgraduate;
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
        $postgraduate = Postgraduate::find($id);
        if ($postgraduate!=null){
            return $postgraduate;
        } else{
            return response()->json(['message'=>'Postgrado no encontrado'],206);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $postgraduate = Postgraduate::find($id);
        if (count([$postgraduate])>0){
            $postgraduateName = Postgraduate::where(['postgraduate_name'=>$request['postgraduate_name']])->get();
            if (count($postgraduateName)>0){
                if ($postgraduateName[0][id]==$id){
                    $postgraduate->update($request->all());
                    $postgraduate = Postgraduate::find($id);
                    return $postgraduate;
                }else{
                    return response()->json(['message'=>'nombre de Postgrado en uso'],206);
                }
            }else{
                $postgraduate->update($request->all());
                $postgraduate = Postgraduate::find($id);
                return $postgraduate;
            }
        }else{
            return response()->json(['message'=>'Postgrado no encontrado'],206);
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
        //
        $postgraduate = Postgraduate::find($id);
        if ($postgraduate!=null){
            $postgraduate->delete();
            return response()->json(['message'=>'OK']);
        }else{
            return response()->json(['message'=>'Postgrado no encontrado'],206);
        }
    }
}
