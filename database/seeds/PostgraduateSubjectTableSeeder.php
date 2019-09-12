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
        PostgraduateSubject::create([
            'postgraduate_id'=>1,
            'subject_id'=>2,
            'type'=>'O',
        ]);
        PostgraduateSubject::create([
            'postgraduate_id'=>1,
            'subject_id'=>3,
            'type'=>'E',
        ]);
        PostgraduateSubject::create([
            'postgraduate_id'=>1,
            'subject_id'=>4,
            'type'=>'E',
        ]);
        PostgraduateSubject::create([
            'postgraduate_id'=>2,
            'subject_id'=>5,
            'type'=>'O',
        ]);
        PostgraduateSubject::create([
            'postgraduate_id'=>2,
            'subject_id'=>6,
            'type'=>'O',
        ]);
        PostgraduateSubject::create([
            'postgraduate_id'=>2,
            'subject_id'=>7,
            'type'=>'E',
        ]);
        PostgraduateSubject::create([
            'postgraduate_id'=>2,
            'subject_id'=>8,
            'type'=>'E',
        ]);
    }
}
