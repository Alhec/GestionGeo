<?php

use Illuminate\Database\Seeder;
use App\Organiation;

class OrganizationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Organiation::query()->truncate();
        Organiation::create([
            'id'=>'G',
            'name'=>'Geoquimica',
            'faculty_id'=>'CIENS'
        ]);
    }
}
