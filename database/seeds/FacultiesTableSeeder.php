<?php

use Illuminate\Database\Seeder;
use App\Faculty;

class FacultiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Faculty::query()->truncate();
        Faculty::create([
            'id'=>'CIENS',
            'name'=>'Facultad de Ciencias',
            'university_id'=>'UCV',
        ]);
    }
}
