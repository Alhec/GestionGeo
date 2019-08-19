<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SchoolPeriod;

class SchoolPeriodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $schoolPeriods = SchoolPeriod::with('subject')->get();
        if (count($schoolPeriods)>0){
            return $schoolPeriods;
        }else{
            return response()->json(['message'=>'No existen periodos escolares'],206);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $schoolPeriod = SchoolPeriod::find($id)->with('subject')->get();
        if (count($schoolPeriod)>0){
            return $schoolPeriod[0];
        }else{
            return response()->json(['message'=>'Periodo escolar no encontrado'],206);
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
        //
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
    }
}
