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
            'inscription_visible'=>true,
            'limit'=>30,
            'enrolled students'=>0,
            'load_notes'=>false,
            'duty'=>10,
        ]);
    }
}
