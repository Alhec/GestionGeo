<?php

use Illuminate\Database\Seeder;
use App\Subject;

class SubjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Subject::query()->truncate();
        Subject::create([
            'subject_code'=>'CC5120263',
            'subject_name'=>'Biomarcadores',
            'uc'=>3,
            'theoretical_hours'=>2,
        ]);
        Subject::create([
            'subject_code'=>'CC5120265',
            'subject_name'=>'PETROFISICA DE EXPLORACION PARA LA DETERMINACION DEL SISTEMA PETROLIFERO',
            'uc'=>3,
            'practical_hours'=>2,
        ]);
        Subject::create([
            'subject_code'=>'CC5120268',
            'subject_name'=>'ESTRATIGRAFIA POR SECUENCIA',
            'uc'=>3,
            'laboratory_hours'=>2,
        ]);
        Subject::create([
            'subject_code'=>'CC5120269',
            'subject_name'=>'MANEJOS DE PASIVOS AMBIENTALES, TRATAMIENTO DE FOSAS',
            'uc'=>3,
            'theoretical_hours'=>2,
        ]);
        Subject::create([
            'subject_code'=>'CC5120272',
            'subject_name'=>'INTERPRETACION DE REGISTROS DE POZOS Y SU RELACION EN EL AREA PETROLERA',
            'uc'=>3,
            'practical_hours'=>2,
        ]);
        Subject::create([
            'subject_code'=>'CC5120273',
            'subject_name'=>'USO DE TRAZADORES ARTIFICIALES Y NATURALES EN ESTUDIOS HIDROLOGICOS',
            'uc'=>3,
            'laboratory_hours'=>2,
        ]);
        Subject::create([
            'subject_code'=>'CC5120274',
            'subject_name'=>'SOLUCIONES ACUOSAS PARA RECUPERACION MEJORADA DE PETROLEO',
            'uc'=>3,
            'theoretical_hours'=>2,
        ]);
        Subject::create([
            'subject_code'=>'CC5120280',
            'subject_name'=>'BIOESTRATIGRAFIA APLICADA A LA GEOQUIMICA DE EXPLORACION',
            'uc'=>3,
            'practical_hours'=>2,
        ]);
        Subject::create([
            'subject_code'=>'CC5120281',
            'subject_name'=>'C.E.I. TERMOCRONOLOGIA POR TRAZAS DE FISION Y APLICACIONES',
            'uc'=>3,
            'laboratory_hours'=>2,
        ]);
        Subject::create([
            'subject_code'=>'CC5120284',
            'subject_name'=>'EMPRESA Y AMBIENTE',
            'uc'=>3,
            'theoretical_hours'=>2,
        ]);
        Subject::create([
            'subject_code'=>'CC5120466',
            'subject_name'=>'TOPICOS ESPECIALES I',
            'uc'=>3,
            'practical_hours'=>2,
        ]);
        Subject::create([
            'subject_code'=>'CC5120472',
            'subject_name'=>'TOPICOS ESPECIALES II',
            'uc'=>3,
            'laboratory_hours'=>2,
        ]);
        Subject::create([
            'subject_code'=>'Tesis',
            'subject_name'=>'Tesis',
            'is_final_subject?'=>true,
            'uc'=>0,
        ]);
        Subject::create([
            'subject_code'=>'Seminario',
            'subject_name'=>'Seminario',
            'uc'=>0,
        ]);
        Subject::create([
            'subject_code'=>'Proyecto',
            'subject_name'=>'Proyeto',
            'is_project_subject?'=>true,
            'uc'=>0,
        ]);


    }
}
