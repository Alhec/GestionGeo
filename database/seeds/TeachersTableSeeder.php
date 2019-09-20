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
            'user_id'=>3,
            'teacher_type'=>'INS', //INS ASI AGR ASO TIT
        ]);
        Teacher::create([
            'user_id'=>4,
            'teacher_type'=>'ASI', //INS ASI AGR ASO TIT
        ]);
        Teacher::create([
            'user_id'=>5,
            'teacher_type'=>'AGR', //INS ASI AGR ASO TIT
        ]);
        Teacher::create([
            'user_id'=>6,
            'teacher_type'=>'ASO', //INS ASI AGR ASO TIT
        ]);
    }
}
