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
            'day'=>'Lunes',
            'classroom'=>'2',
            'start_hour'=>now(),
            'end_hour'=>now(),
        ]);
    }
}
