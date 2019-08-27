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
            'subject_name'=>'Carbon',
            'uc'=>5,
        ]);
        Subject::create([
            'subject_code'=>'12346',
            'subject_name'=>'Develop',
            'uc'=>5,
        ]);
    }
}
