<?php

use App\DoctoralExam;
use Illuminate\Database\Seeder;

class DoctoralExamTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DoctoralExam::query()->truncate();
    }
}
