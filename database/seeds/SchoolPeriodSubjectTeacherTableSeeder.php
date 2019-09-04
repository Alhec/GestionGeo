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
            'teacher_id'=>1,
            'subject_id'=>1,
            'school_period_id'=>1,
            'limit'=>30,
            'enrolled_students'=>0,
            'duty'=>10,
        ]);
        SchoolPeriodSubjectTeacher::create([
            'teacher_id'=>2,
            'subject_id'=>2,
            'school_period_id'=>2,
            'limit'=>30,
            'enrolled_students'=>0,
            'duty'=>10,
        ]);
    }
}
