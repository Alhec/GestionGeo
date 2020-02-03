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
            'cod_school_period'=>'sem1',
            'start_date'=>'2019-12-30',
            'end_date'=>'2020-01-03',
            'withdrawal_deadline'=>'2019-12-31',
            'inscription_start_date'=>'2019-12-30',
            'inscription_visible'=>false,
            'organization_id'=>'ICT',
            'load_notes'=>false,
        ]);
        SchoolPeriod::create([
            'cod_school_period'=>'sem2',
            'start_date'=>'2020-01-06',
            'end_date'=>'2020-01-10',
            'inscription_start_date'=>'2020-01-06',
            'withdrawal_deadline'=>'2020-01-07',
            'inscription_visible'=>false,
            'organization_id'=>'ICT',
            'load_notes'=>false,
        ]);
        SchoolPeriod::create([
            'cod_school_period'=>'sem3',
            'start_date'=>'2020-01-13',
            'end_date'=>'2020-01-17',
            'withdrawal_deadline'=>'2020-01-14',
            'inscription_start_date'=>'2020-01-13',
            'inscription_visible'=>false,
            'organization_id'=>'ICT',
            'load_notes'=>false,
        ]);
        SchoolPeriod::create([
            'cod_school_period'=>'sem4',
            'start_date'=>'2020-01-20',
            'end_date'=>'2020-01-24',
            'withdrawal_deadline'=>'2020-01-21',
            'inscription_start_date'=>'2020-01-20',
            'inscription_visible'=>false,
            'organization_id'=>'ICT',
            'load_notes'=>false,
        ]);
        SchoolPeriod::create([
            'cod_school_period'=>'sem5',
            'start_date'=>'2020-01-27',
            'end_date'=>'2020-01-31',
            'inscription_start_date'=>'2020-01-27',
            'withdrawal_deadline'=>'2020-01-28',
            'inscription_visible'=>false,
            'organization_id'=>'ICT',
            'load_notes'=>false,
        ]);
        SchoolPeriod::create([
            'cod_school_period'=>'sem6',
            'start_date'=>'2020-02-03',
            'end_date'=>'2020-02-07',
            'withdrawal_deadline'=>'2020-02-04',
            'inscription_start_date'=>'2020-02-03',
            'inscription_visible'=>true,
            'organization_id'=>'ICT',
            'load_notes'=>false,
        ]);
    }
}
