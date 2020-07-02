<?php

use Illuminate\Database\Seeder;
use App\FinalWorkSchoolPeriod;

class FinalWorkSchoolPeriodTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FinalWorkSchoolPeriod::query()->truncate();
        /*FinalWorkSchoolPeriod::create([
            'final_work_id'=>1,
            'school_period_student_id'=>1,
        ]);
        FinalWorkSchoolPeriod::create([
            'final_work_id'=>2,
            'school_period_student_id'=>2,
        ]);*/
    }
}
