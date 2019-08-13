<?php

use Illuminate\Database\Seeder;
use App\Teacher;


class TeachersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        Teacher::query()->truncate();
        Teacher::create([
            'user_id'=>2,
            'teacher_type'=>'INS', //INS ASI AGR ASO TIT
            'level_instruction'=>'Doctor',
            'full_time'=>true,
        ]);

    }
}
