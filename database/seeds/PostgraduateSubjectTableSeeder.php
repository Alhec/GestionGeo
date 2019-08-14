<?php

use Illuminate\Database\Seeder;
use App\PostgraduateSubject;

class PostgraduateSubjectTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        PostgraduateSubject::query()->truncate();
        PostgraduateSubject::create([
            'postgraduate_id'=>1,
            'subject_id'=>1,
            'type'=>'O',
        ]);
    }
}
