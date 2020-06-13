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
            'code'=>'CC5120263',
            'name'=>'Biomarcadores',
            'uc'=>3,
            'theoretical_hours'=>2,
        ]);
        Subject::create([
            'code'=>'CC5120265',
            'name'=>'PETROFISICA DE EXPLORACION PARA LA DETERMINACION DEL SISTEMA PETROLIFERO',
            'uc'=>3,
            'practical_hours'=>2,
        ]);
        Subject::create([
            'code'=>'CC5120268',
            'name'=>'ESTRATIGRAFIA POR SECUENCIA',
            'uc'=>3,
            'laboratory_hours'=>2,
        ]);
        Subject::create([
            'code'=>'CC5120269',
            'name'=>'MANEJOS DE PASIVOS AMBIENTALES, TRATAMIENTO DE FOSAS',
            'uc'=>3,
            'theoretical_hours'=>2,
        ]);
        Subject::create([
            'code'=>'CC5120272',
            'name'=>'INTERPRETACION DE REGISTROS DE POZOS Y SU RELACION EN EL AREA PETROLERA',
            'uc'=>3,
            'practical_hours'=>2,
        ]);
        Subject::create([
            'code'=>'CC5120273',
            'name'=>'USO DE TRAZADORES ARTIFICIALES Y NATURALES EN ESTUDIOS HIDROLOGICOS',
            'uc'=>3,
            'laboratory_hours'=>2,
        ]);
        Subject::create([
            'code'=>'CC5120274',
            'name'=>'SOLUCIONES ACUOSAS PARA RECUPERACION MEJORADA DE PETROLEO',
            'uc'=>3,
            'theoretical_hours'=>2,
        ]);
        Subject::create([
            'code'=>'CC5120280',
            'name'=>'BIOESTRATIGRAFIA APLICADA A LA GEOQUIMICA DE EXPLORACION',
            'uc'=>3,
            'practical_hours'=>2,
        ]);
        Subject::create([
            'code'=>'CC5120281',
            'name'=>'C.E.I. TERMOCRONOLOGIA POR TRAZAS DE FISION Y APLICACIONES',
            'uc'=>3,
            'laboratory_hours'=>2,
        ]);
        Subject::create([
            'code'=>'CC5120284',
            'name'=>'EMPRESA Y AMBIENTE',
            'uc'=>3,
            'theoretical_hours'=>2,
        ]);
        Subject::create([
            'code'=>'CC5120466',
            'name'=>'TOPICOS ESPECIALES I',
            'uc'=>3,
            'practical_hours'=>2,
        ]);
        Subject::create([
            'code'=>'CC5120472',
            'name'=>'TOPICOS ESPECIALES II',
            'uc'=>3,
            'laboratory_hours'=>2,
        ]);
        Subject::create([
            'code'=>'Tesis',
            'name'=>'Tesis',
            'is_final_subject'=>true,
            'uc'=>0,
        ]);
        Subject::create([
            'code'=>'Seminario',
            'name'=>'Seminario',
            'uc'=>0,
        ]);
        Subject::create([
            'code'=>'Proyecto',
            'name'=>'Proyeto',
            'is_project_subject'=>true,
            'uc'=>0,
        ]);
    }
}
