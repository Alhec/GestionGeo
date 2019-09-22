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
            'school_period_subject_teacher_id'=>3,
            'day'=>'Lunes',
            'classroom'=>'2',
            'start_hour'=>'07:00:00',
            'end_hour'=>'09:00:00',
        ]);
    }
}
