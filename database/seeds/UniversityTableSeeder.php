<?php

use Illuminate\Database\Seeder;
use App\University;

class UniversityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        University::query()->truncate();
        University::create([
           'id'=>'UCV',
           'name'=>'Universidad Central de Venezuela'
        ]);
    }
}
