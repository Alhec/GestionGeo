<?php

use Illuminate\Database\Seeder;
use App\Subject;

class SubjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Subject::query()->truncate();
        Subject::create([
            'subject_code'=>'12345',
            'subject_name'=>'Aire',
            'uc'=>5,
        ]);
        Subject::create([
            'subject_code'=>'12346',
            'subject_name'=>'Tierra',
            'uc'=>5,
        ]);
        Subject::create([
            'subject_code'=>'12347',
            'subject_name'=>'Fuego',
            'uc'=>5,
        ]);
        Subject::create([
            'subject_code'=>'12348',
            'subject_name'=>'Agua',
            'uc'=>5,
        ]);
        Subject::create([
            'subject_code'=>'22345',
            'subject_name'=>'BI',
            'uc'=>5,
        ]);
        Subject::create([
            'subject_code'=>'22346',
            'subject_name'=>'BPM',
            'uc'=>5,
        ]);
        Subject::create([
            'subject_code'=>'22347',
            'subject_name'=>'OACA',
            'uc'=>5,
        ]);
        Subject::create([
            'subject_code'=>'22348',
            'subject_name'=>'AyP',
            'uc'=>5,
        ]);
    }
}
