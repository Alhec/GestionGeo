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
            'subject_name'=>'Materia1',
            'uc'=>5,
        ]);
    }
}
