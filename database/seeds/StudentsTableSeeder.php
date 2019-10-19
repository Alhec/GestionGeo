<?php

use Illuminate\Database\Seeder;
use App\Student;

class StudentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Student::query()->truncate();
        Student::create([
            'school_program_id'=>1,
            'user_id'=>7,
            'student_type'=>"REG", // REG EXT AMP
            'home_university'=>'Universidad Central de Venezuela',
            'is_ucv_teacher?'=>false,
            'is_available_final_work' => false,
            'repeat_approved_subject' =>false,
            'repeat_reprobated_subject'=>false,
            'guide_teacher_id'=>1

        ]);
        Student::create([
            'school_program_id'=>3,
            'user_id'=>8,
            'student_type'=>"EXT", // REG EXT AMP
            'home_university'=>'Universidad Central de Venezuela',
            'is_ucv_teacher'=>false,
            'is_available_final_work' => false,
            'repeat_approved_subject' =>false,
            'repeat_reprobated_subject'=>false,
        ]);
        Student::create([
            'school_program_id'=>3,
            'user_id'=>9,
            'student_type'=>"AMP", // REG EXT AMP
            'home_university'=>'Universidad Central de Venezuela',
            'is_ucv_teacher'=>false,
            'is_available_final_work' => false,
            'repeat_approved_subject' =>false,
            'repeat_reprobated_subject'=>false,
        ]);
        Student::create([
            'school_program_id'=>3,
            'user_id'=>10,
            'student_type'=>"REG", // REG EXT AMP
            'home_university'=>'Universidad Central de Venezuela',
            'is_ucv_teacher'=>false,
            'is_available_final_work' => false,
            'repeat_approved_subject' =>false,
            'repeat_reprobated_subject'=>false,
        ]);
        Student::create([
            'school_program_id'=>2,
            'user_id'=>11,
            'student_type'=>"EXT", // REG EXT AMP
            'home_university'=>'Universidad Central de Venezuela',
            'is_ucv_teacher'=>false,
            'is_available_final_work' => false,
            'repeat_approved_subject' =>false,
            'repeat_reprobated_subject'=>false,
        ]);
        Student::create([
            'school_program_id'=>2,
            'user_id'=>12,
            'student_type'=>"AMP", // REG EXT AMP
            'home_university'=>'Universidad Central de Venezuela',
            'is_ucv_teacher'=>false,
            'is_available_final_work' => false,
            'repeat_approved_subject' =>false,
            'repeat_reprobated_subject'=>false,
        ]);
    }
}
