<?php

use Illuminate\Database\Seeder;
use App\Advisor;

class AdvisorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Advisor::query()->truncate();
        Advisor::create([
            'final_work_id'=>1,
            'teacher_id'=>3
        ]);
        Advisor::create([
            'final_work_id'=>2,
            'teacher_id'=>3
        ]);
        Advisor::create([
            'final_work_id'=>2,
            'teacher_id'=>4
        ]);
    }
}
