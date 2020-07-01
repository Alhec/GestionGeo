<?php

use Illuminate\Database\Seeder;
use App\Schedule;

class SchedulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schedule::query()->truncate();
        Schedule::create([
            'school_period_subject_teacher_id'=>1,
            'day'=>'1',
            'classroom'=>'1',
            'start_hour'=>'07:00:00',
            'end_hour'=>'09:00:00',
        ]);
        Schedule::create([
            'school_period_subject_teacher_id'=>2,
            'day'=>'2',
            'classroom'=>'2',
            'start_hour'=>'11:00:00',
            'end_hour'=>'13:00:00',
        ]);
        Schedule::create([
            'school_period_subject_teacher_id'=>3,
            'day'=>'3',
            'classroom'=>'2',
            'start_hour'=>'15:00:00',
            'end_hour'=>'17:00:00',
        ]);
    }
}
