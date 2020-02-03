<?php

use Illuminate\Database\Seeder;
use App\SchoolPeriodSubjectTeacher;

class SchoolPeriodSubjectTeacherTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SchoolPeriodSubjectTeacher::query()->truncate();
        SchoolPeriodSubjectTeacher::create([
            'teacher_id'=>3,
            'subject_id'=>1,
            'school_period_id'=>1,
            'limit'=>30,
            'enrolled_students'=>0,
            'duty'=>10,
            'modality'=>'REG'
        ]);
    }
}
