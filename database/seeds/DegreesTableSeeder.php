<?php

use Illuminate\Database\Seeder;
use App\Degree;

class DegreesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Degree::query()->truncate();
        Degree::create([
            'student_id'=>1,
            'degree'=>'Licenciado',
            'date_acquired'=>now(),
        ]);


    }
}
