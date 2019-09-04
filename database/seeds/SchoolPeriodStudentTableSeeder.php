<?php

use Illuminate\Database\Seeder;
use App\SchoolPeriodStudent;

class SchoolPeriodStudentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SchoolPeriodStudent::query()->truncate();
        SchoolPeriodStudent::create([
            'student_id'=>1,
            'school_period_id'=>1,
            'pay_ref'=>'1234567890',
            'status'=>'INC-A', //RET-A RET-B DES-A DES-B INC-A INC-B REI-A REI-B REG
        ]);
        SchoolPeriodStudent::create([
            'student_id'=>2,
            'school_period_id'=>2,
            'pay_ref'=>'1234567890',
            'status'=>'INC-A', //RET-A RET-B DES-A DES-B INC-A INC-B REI-A REI-B REG
        ]);
    }
}
