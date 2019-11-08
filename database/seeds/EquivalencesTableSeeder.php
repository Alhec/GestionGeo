<?php

use App\Equivalence;
use Illuminate\Database\Seeder;

class EquivalencesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Equivalence::query()->truncate();
        Equivalence::create([
            'student_id'=>1,
            'subject_id'=>1,
            'qualification'=>14
        ]);
    }
}
