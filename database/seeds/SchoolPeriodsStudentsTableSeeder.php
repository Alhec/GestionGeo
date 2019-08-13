<?php

use Illuminate\Database\Seeder;
use App\SchoolPeriodStudent;

class SchoolPeriodsStudentsTableSeeder extends Seeder
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
            'duty'=>10,
            'pay_ref'=>'referencia de pago',
            'status'=>'INC-A', //RET-A RET-B DES-A DES-B INC-A INC-B REI-A REI-B REG
        ]);
    }
}
