<?php

use Illuminate\Database\Seeder;
use App\SchoolProgram;

class SchoolProgramsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SchoolProgram::query()->truncate();
        SchoolProgram::create([
            'school_program_name' => 'Especializacion de Geoquimica 2',
            'num_cu' => 50,
            'organization_id'=>'G',
            'duration'=>3,
            'conducive_to_degree'=>true
        ]);
        SchoolProgram::create([
            'school_program_name' => 'Especializacion en Computacion',
            'num_cu' => 50,
            'organization_id'=>'C',
            'duration'=>3,
            'conducive_to_degree'=>true
        ]);
        SchoolProgram::create([
            'school_program_name' => 'Especializacion de Geoquimica',
            'num_cu' => 10,
            'organization_id'=>'G',
            'duration'=>3,
            'conducive_to_degree'=>true
        ]);
    }
}
