<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Subject;
use App\PostgraduateSubject;
use App\Postgraduate;

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
        if (count($subjects)>0){
            return $subjects;
        }else{
            return response()->json(['message'=>'No existen materias'],206);
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
        $subject=Subject::where('subject_name',$request['subject_name'])->get();
        if (count($subject)<=0){ //valido que la materia a crear no exista (por nombre)
            $postgraduatesInBd=Postgraduate::all('id');
            //validacion de los postgrados enviados si existen en bd
            $validPostgraduates = true;
            $postgraduates = $request['postgraduates'];
            foreach ($postgraduates as $postgraduate){
                $found = false;
                foreach ($postgraduatesInBd as $postgraduateInBd){
                    if (($postgraduate['id']!= $postgraduateInBd['id'])AND $found ==false){
                        $validPostgraduates=false;
                    }else{
                        $found =true;
                        $validPostgraduates=true;
                    }
                }
            }
            if ($validPostgraduates == false){
                return response()->json(['message'=>'Postgrados invalidos'],206);
            }else{//si lospostgrados son validos se crea la materia asignada a los postgrados
                Subject::create($request->all());
                $subject = Subject::where('subject_name',$request['subject_name'])->get()[0];
                $postgraduates = $request['postgraduates'];
                $cant_postgraduates=sizeof($postgraduates);
                for ($i=0;$i<$cant_postgraduates;$i++){
                    PostgraduateSubject::create(['postgraduate_id'=>$postgraduates[0]['id'],
                        'subject_id'=>$subject['id'],
                        'type'=>$postgraduates[0]['type'],]);
                }
                $subjectReturn = Subject::with('postgraduates')->find($subject['id']);
                return $subjectReturn;
            }
        }else{
            return response()->json(['message'=>'Materia existente'],206);
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
        $subject = Subject::with('postgraduates')->find($id);
        if ($subject != null){
            return $subject;
        }else{
            return response()->json(['message'=>'Materia no encontrada'],206);
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
        $subject=Subject::find($id);
        if ($subject==null){ //validar que la materia exista
            return response()->json(['message'=>'Materia no encontrada'],206);
        }else{
            $nameSubject = Subject::where('subject_name',$request['subject_name'])->get();
            if (count($nameSubject)>0){//el nombre existe en bd
                if ($nameSubject[0]['id']==$id){//El nombre es del mismo registro a editar
                    $postgraduatesInBd=Postgraduate::all('id');
                    //validacion de los postgrados enviados
                    $validPostgraduates = true;
                    $postgraduates = $request['postgraduates'];
                    foreach ($postgraduates as $postgraduate){
                        $found = false;
                        foreach ($postgraduatesInBd as $postgraduateInBd){
                            if (($postgraduate['id']!= $postgraduateInBd['id'])AND $found ==false){
                                $validPostgraduates=false;
                            }else{
                                $found =true;
                                $validPostgraduates=true;
                            }
                        }
                    }
                    if ($validPostgraduates == false){
                        return response()->json(['message'=>'Postgrados invalidos'],206);
                    }else{
                        $subject->update($request->all());
                        $postgraduatesID = PostgraduateSubject::where('subject_id',$id)->get(['id','postgraduate_id']);
                        foreach ($postgraduates as $postgraduate){
                            $existPostgraduate=false;
                            foreach ($postgraduatesID as $postgraduateID){
                                if ($postgraduate['id']==$postgraduateID['postgraduate_id']){
                                    $postgraduate['subject_id']=$id;
                                    $postgraduateUpd=PostgraduateSubject::where('postgraduate_id',$postgraduateID['postgraduate_id'])
                                        ->where('subject_id',$id)->get()[0];
                                    $postgraduateUpd->update($postgraduate);
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
                        $subjectReturn = Subject::with('postgraduates')->find($subject['id']);
                        return $subjectReturn;
                    }
                }else{
                    return response()->json(['message'=>'Nombre de materia en uso'],206);
                }
            }else{//el nombre esta disponible
                $postgraduatesInBd=Postgraduate::all('id');
                //validacion de los postgrados enviados
                $validPostgraduates = true;
                $postgraduates = $request['postgraduates'];
                foreach ($postgraduates as $postgraduate){
                    $found = false;
                    foreach ($postgraduatesInBd as $postgraduateInBd){
                        if (($postgraduate['id']!= $postgraduateInBd['id'])AND $found ==false){
                            $validPostgraduates=false;
                        }else{
                            $found =true;
                            $validPostgraduates=true;
                        }
                    }
                }
                if ($validPostgraduates == false){
                    return response()->json(['message'=>'Postgrados invalidos'],206);
                }else{
                    $subject->update($request->all());
                    $postgraduatesID = PostgraduateSubject::where('subject_id',$id)->get(['id','postgraduate_id']);
                    foreach ($postgraduates as $postgraduate){
                        $existPostgraduate=false;
                        foreach ($postgraduatesID as $postgraduateID){
                            if ($postgraduate['id']==$postgraduateID['postgraduate_id']){
                                $postgraduate['subject_id']=$id;
                                $postgraduateUpd=PostgraduateSubject::where('postgraduate_id',$postgraduateID['postgraduate_id'])
                                    ->where('subject_id',$id)->get()[0];
                                $postgraduateUpd->update($postgraduate);
                                $existPostgraduate=true;
                            }
                        }
                        if ($existPostgraduate==false){
                            PostgraduateSubject::create(['postgraduate_id'=>$postgraduate['id'],
                                'subject_id'=>$id,
                                'type'=>$postgraduate['type'],
                            ]);
                        }
                    }
                    $subjectReturn = Subject::with('postgraduates')->find($subject['id']);
                    return $subjectReturn;
                }
            }
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
        $subject = Subject::find($id);
        if ($subject!=null){
            $subject->delete();
            return response()->json(['message'=>'OK']);
        }else{
            return response()->json(['message'=>'Materia no encontrada'],206);
        }

    }
}
