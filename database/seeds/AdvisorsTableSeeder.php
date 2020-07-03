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
            'final_work_id'=>3,
            'teacher_id'=>3
        ]);

    }
}
