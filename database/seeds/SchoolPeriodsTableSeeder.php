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
            'cod_school_period'=>'1-2019',
            'start_date'=>'2019-01-01',
            'end_date'=>'2019-06-30',
            'withdrawal_deadline'=>'2019-03-01',
            'inscription_visible'=>false,
            'organization_id'=>'G',
            'load_notes'=>false,
        ]);
        SchoolPeriod::create([
            'cod_school_period'=>'2-2019',
            'start_date'=>'2019-06-01',
            'end_date'=>'2019-12-31',
            'withdrawal_deadline'=>'2019-09-01',
            'inscription_visible'=>true,
            'organization_id'=>'G',
            'load_notes'=>false,
        ]);
        SchoolPeriod::create([
            'cod_school_period'=>'2-2019',
            'start_date'=>'2019-06-01',
            'end_date'=>'2019-12-31',
            'withdrawal_deadline'=>'2019-09-01',
            'inscription_visible'=>false,
            'organization_id'=>'C',
            'load_notes'=>false,
        ]);
    }
}
