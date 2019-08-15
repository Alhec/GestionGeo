<?php

use Illuminate\Database\Seeder;
use App\StudentSubject;

class StudentsSubjectsTableSeeder extends Seeder
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
            'student_id'=>1,
            'school_period_subject_teacher_id'=>1,
            'qualification'=>0,
            'status'=>'CUR' //CUR RET APR REP
        ]);
    }
}
