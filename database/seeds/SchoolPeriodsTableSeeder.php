<?php

use Illuminate\Database\Seeder;
use App\SchoolPeriod;
class SchoolPeriodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SchoolPeriod::query()->truncate();
        SchoolPeriod::create([
            'cod_school_period'=>'2-2019',
            'start_date'=>now(),
            'end_date'=>now(),
            'duty'=>10,
            'inscription_visible'=>true,
            'end_school_period'=>false,
        ]);
    }
}
