<?php

use Illuminate\Database\Seeder;
use App\Organization;

class OrganizationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Organization::query()->truncate();
        Organization::create([
            'id'=>'G',
            'name'=>'Geoquimica',
            'faculty_id'=>'CIENS'
        ]);
        Organization::create([
            'id'=>'C',
            'name'=>'Computacion',
            'faculty_id'=>'CIENS'
        ]);
        Organization::create([
            'id'=>'ICT',
            'name'=>'Instituto de Ciencias de la Tierra',
            'faculty_id'=>'CIENS',
            'organization_id'=>'G'
        ]);
    }
}
