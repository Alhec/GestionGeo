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
            'start_date'=>'2019-05-10',
            'end_date'=>'2019-10-10',
            'inscription_visible'=>true,
            'organization_id'=>'G',
            'load_notes'=>false,
        ]);
        SchoolPeriod::create([
            'cod_school_period'=>'2-2019',
            'start_date'=>'2019-05-10',
            'end_date'=>'2019-10-10',
            'inscription_visible'=>true,
            'organization_id'=>'C',
            'load_notes'=>false,
        ]);
    }
}
