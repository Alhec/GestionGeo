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
            'subject_code'=>'12345',
            'subject_name'=>'Geodinamica Avanzada',
            'uc'=>5,
            'is_final_subject?'=>false,
            'theoretical_hours'=>1,
            'practical_hours'=>1,
            'laboratory_hours'=>1
        ]);
        Subject::create([
            'subject_code'=>'12346',
            'subject_name'=>'Geoquimica General',
            'uc'=>5,
            'is_final_subject?'=>false,
            'theoretical_hours'=>1,
            'practical_hours'=>1,
            'laboratory_hours'=>1
        ]);
        Subject::create([
            'subject_code'=>'12347',
            'subject_name'=>'Petrologia Ignea',
            'uc'=>5,
            'is_final_subject?'=>false,
            'theoretical_hours'=>1,
            'practical_hours'=>1,
            'laboratory_hours'=>1
        ]);
        Subject::create([
            'subject_code'=>'12348',
            'subject_name'=>'Petrologia Metamorfica',
            'uc'=>5,
            'is_final_subject?'=>false,
            'theoretical_hours'=>1,
            'practical_hours'=>1,
            'laboratory_hours'=>1
        ]);
        Subject::create([
            'subject_code'=>'22345',
            'subject_name'=>'BI',
            'uc'=>5,
            'is_final_subject?'=>false,
            'theoretical_hours'=>1,
            'practical_hours'=>1,
            'laboratory_hours'=>1
        ]);
        Subject::create([
            'subject_code'=>'22346',
            'subject_name'=>'BPM',
            'uc'=>5,
            'is_final_subject?'=>false,
            'theoretical_hours'=>1,
            'practical_hours'=>1,
            'laboratory_hours'=>1
        ]);
        Subject::create([
            'subject_code'=>'22347',
            'subject_name'=>'OACA',
            'uc'=>5,
            'is_final_subject?'=>false,
            'theoretical_hours'=>1,
            'practical_hours'=>1,
            'laboratory_hours'=>1
        ]);
        Subject::create([
            'subject_code'=>'22348',
            'subject_name'=>'AyP',
            'uc'=>5,
            'is_final_subject?'=>false,
            'theoretical_hours'=>1,
            'practical_hours'=>1,
            'laboratory_hours'=>1
        ]);
    }
}
