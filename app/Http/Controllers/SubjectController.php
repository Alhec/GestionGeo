<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Subject;
use App\PostgraduateSubject;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $subjects = Subject::with('postgraduates')->get();
        return $subjects;
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
        Subject::create($request->all());
        $subject = Subject::where('subject_name',$request['subject_name'])->get()[0];
        $postgraduates = $request['postgraduates'];
        $cant_postgraduates=sizeof($postgraduates);
        for ($i=0;$i<$cant_postgraduates;$i++){
            PostgraduateSubject::create(['postgraduate_id'=>$postgraduates[0]['id'],
                'subject_id'=>$subject['id'],
                'type'=>$postgraduates[0]['type'],]);
        }
        return response()->json(['message'=>'OK']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $subject = Subject::with('postgraduates')->find($id);
        return $subject;
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

    public function includeInBd($postgraduate,$postgraduatesInBd){

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
        Subject::find($id)->update($request->all());
        $postgraduates = $request['postgraduates'];
        $postgraduatesID = PostgraduateSubject::where('subject_id',$id)->get(['id','postgraduate_id']);
        foreach ($postgraduates as $postgraduate){
            $existPostgraduate=false;
            foreach ($postgraduatesID as $postgraduateID){
                if ($postgraduate['id']==$postgraduateID['postgraduate_id']){
                    $postgraduate['subject_id']=$id;
                    PostgraduateSubject::find($postgraduateID['id'])->update($postgraduate);
                    $existPostgraduate=true;
                    break;
                }
            }
            if ($existPostgraduate==false){
                PostgraduateSubject::create(['postgraduate_id'=>$postgraduate['id'],
                    'subject_id'=>$id,
                    'type'=>$postgraduate['type'],
                    ]);
            }
        }
        return response()->json(['message'=>'OK']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Subject::find($id)->delete();
        return response()->json(['message'=>'OK']);
    }
}
