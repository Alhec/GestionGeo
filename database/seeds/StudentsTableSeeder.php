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
            'postgraduate_id'=>1,
            'user_id'=>7,
            'student_type'=>"REG", // REG EXT AMP
            'home_university'=>'Universidad Central de Venezuela',
        ]);
        Student::create([
            'postgraduate_id'=>3,
            'user_id'=>8,
            'student_type'=>"EXT", // REG EXT AMP
            'home_university'=>'Universidad Central de Venezuela',
        ]);
        Student::create([
            'postgraduate_id'=>3,
            'user_id'=>9,
            'student_type'=>"AMP", // REG EXT AMP
            'home_university'=>'Universidad Central de Venezuela',
        ]);
        Student::create([
            'postgraduate_id'=>3,
            'user_id'=>10,
            'student_type'=>"REG", // REG EXT AMP
            'home_university'=>'Universidad Central de Venezuela',
        ]);
        Student::create([
            'postgraduate_id'=>2,
            'user_id'=>11,
            'student_type'=>"EXT", // REG EXT AMP
            'home_university'=>'Universidad Central de Venezuela',
        ]);
        Student::create([
            'postgraduate_id'=>2,
            'user_id'=>12,
            'student_type'=>"AMP", // REG EXT AMP
            'home_university'=>'Universidad Central de Venezuela',
        ]);
    }
}
