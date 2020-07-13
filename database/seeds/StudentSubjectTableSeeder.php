<?php

use Illuminate\Database\Seeder;
use App\StudentSubject;

class StudentSubjectTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        StudentSubject::query()->truncate();
        StudentSubject::create([
            'school_period_student_id'=>1,
            'school_period_subject_teacher_id'=>1,
            'qualification'=>15,
            'status'=>'APR' //CUR RET APR REP
        ]);
        StudentSubject::create([
            'school_period_student_id'=>2,
            'school_period_subject_teacher_id'=>2,
            'qualification'=>16,
            'status'=>'APR' //CUR RET APR REP
        ]);
        StudentSubject::create([
            'school_period_student_id'=>4,
            'school_period_subject_teacher_id'=>3,
            'qualification'=>16,
            'status'=>'APR' //CUR RET APR REP
        ]);
    }
}
