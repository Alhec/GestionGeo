<?php

use Illuminate\Database\Seeder;
use App\FinalWork;

class FinalWorkTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FinalWork::query()->truncate();
        /*FinalWork::create([
            'title'=>'proyecto',
            'student_id'=>1,
            'subject_id'=>15,
            'is_project'=>true,
            'approval_date'=>'2020-01-09'
        ]);
        FinalWork::create([
            'title'=>'tesis',
            'student_id'=>1,
            'subject_id'=>13,
            'project_id'=>1,
            'approval_date'=>'2020-01-09'
        ]);*/
    }
}
