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
            'id'=>3,
            'teacher_type'=>'INS', //INS ASI AGR ASO TIT INV
            'dedication'=>'EXC'
        ]);
        Teacher::create([
            'id'=>4,
            'teacher_type'=>'ASI', //INS ASI AGR ASO TIT INV
            'dedication'=>'TC'
        ]);
        Teacher::create([
            'id'=>5,
            'teacher_type'=>'AGR', //INS ASI AGR ASO TIT INV
            'dedication'=>'CON'
        ]);
        Teacher::create([
            'id'=>6,
            'teacher_type'=>'ASO', //INS ASI AGR ASO TIT INV
            'dedication'=>'MT'
        ]);
        Teacher::create([
            'id'=>7,
            'teacher_type'=>'TIT', //INS ASI AGR ASO TIT INV
            'dedication'=>'INV'
        ]);
        Teacher::create([
            'id'=>20,
            'teacher_type'=>'INS', //INS ASI AGR ASO TIT INV
            'dedication'=>'EXC'
        ]);
        Teacher::create([
            'id'=>21,
            'teacher_type'=>'ASI', //INS ASI AGR ASO TIT INV
            'dedication'=>'TC'
        ]);
        Teacher::create([
            'id'=>22,
            'teacher_type'=>'AGR', //INS ASI AGR ASO TIT INV
            'dedication'=>'CON'
        ]);
        Teacher::create([
            'id'=>23,
            'teacher_type'=>'ASO', //INS ASI AGR ASO TIT INV
            'dedication'=>'MT'
        ]);
        Teacher::create([
            'id'=>24,
            'teacher_type'=>'TIT', //INS ASI AGR ASO TIT INV
            'dedication'=>'INV'
        ]);
    }
}
