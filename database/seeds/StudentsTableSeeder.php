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
            'user_id'=>3,
            'student_type'=>"REG", // REG EXT AMP
            'level_instruction'=>'Licenciado',
            'home_university'=>'Universidad Central de Venezuela',
        ]);
    }
}
