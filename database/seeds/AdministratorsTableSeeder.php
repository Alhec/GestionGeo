<?php

use Illuminate\Database\Seeder;
use App\Administrator;

class AdministratorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Administrator::query()->truncate();
        Administrator::create([
            'user_id'=>1,
            'rol'=>'COORDINATOR',
            'principal'=>true
        ]);
        Administrator::create([
            'user_id'=>2,
            'rol'=>'COORINATOR',
            'principal'=>true
        ]);
    }
}
