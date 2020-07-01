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
            'degree_obtained'=>'Lic',
            'degree_name'=>'Licenciado en Geoquimica',
            'university'=>'Universidad Central de Venezuela'
        ]);
        Degree::create([
            'student_id'=>2,
            'degree_obtained'=>'Lic',
            'degree_name'=>'Licenciado en Geoquimica',
            'university'=>'Universidad Central de Venezuela'
        ]);
        Degree::create([
            'student_id'=>3,
            'degree_obtained'=>'Lic',
            'degree_name'=>'Licenciado en Geoquimica',
            'university'=>'Universidad Central de Venezuela'
        ]);
        Degree::create([
            'student_id'=>4,
            'degree_obtained'=>'Lic',
            'degree_name'=>'Licenciado en Geoquimica',
            'university'=>'Universidad Central de Venezuela'
        ]);
        Degree::create([
            'student_id'=>5,
            'degree_obtained'=>'Lic',
            'degree_name'=>'Licenciado en Geoquimica',
            'university'=>'Universidad Central de Venezuela'
        ]);
        Degree::create([
            'student_id'=>6,
            'degree_obtained'=>'Lic',
            'degree_name'=>'Licenciado en Geoquimica',
            'university'=>'Universidad Central de Venezuela'
        ]);
        Degree::create([
            'student_id'=>7,
            'degree_obtained'=>'Lic',
            'degree_name'=>'Licenciado en Geoquimica',
            'university'=>'Universidad Central de Venezuela'
        ]);
        Degree::create([
            'student_id'=>8,
            'degree_obtained'=>'Lic',
            'degree_name'=>'Licenciado en Geoquimica',
            'university'=>'Universidad Central de Venezuela'
        ]);
        Degree::create([
            'student_id'=>9,
            'degree_obtained'=>'Lic',
            'degree_name'=>'Licenciado en Geoquimica',
            'university'=>'Universidad Central de Venezuela'
        ]);
        Degree::create([
            'student_id'=>10,
            'degree_obtained'=>'Lic',
            'degree_name'=>'Licenciado en Geoquimica',
            'university'=>'Universidad Central de Venezuela'
        ]);
    }
}
