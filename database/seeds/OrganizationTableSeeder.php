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
    }
}
