<?php

use Illuminate\Database\Seeder;
use App\FinalWork;

class FinalWorkTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FinalWork::query()->truncate();
        FinalWork::create([
            'title'=>'proyecto estudiante 1',
            'student_id'=>1,
            'subject_id'=>15,
            'is_project'=>true,
            'approval_date'=>'2020-03-30'
        ]);
        FinalWork::create([
            'title'=>'seminario estudiante 1',
            'student_id'=>1,
            'subject_id'=>14,
            'is_project'=>true,
            'approval_date'=>'2020-03-29'
        ]);
        FinalWork::create([
            'title'=>'tesis estudiante 1',
            'student_id'=>1,
            'subject_id'=>13,
            'project_id'=>1,
            'approval_date'=>'2020-05-30'
        ]);
        FinalWork::create([
            'title'=>'proyecto estudiante 9',
            'student_id'=>9,
            'subject_id'=>15,
            'is_project'=>true,
            'approval_date'=>'2020-03-30'
        ]);
        FinalWork::create([
            'title'=>'seminario estudiante 9',
            'student_id'=>9,
            'subject_id'=>14,
            'is_project'=>true,
            'approval_date'=>'2020-03-29'
        ]);
    }
}
