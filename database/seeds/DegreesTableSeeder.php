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
            'degree_name'=>'Licenciado en Geoquimica',
            'degree_description'=>'Licenciado con mencion en Mineria'
        ]);
    }
}
