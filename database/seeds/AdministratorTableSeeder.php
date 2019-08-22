<?php

use Illuminate\Database\Seeder;
use App\Administrator;

class AdministratorTableSeeder extends Seeder
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
            'user_id'=>1
        ]);
    }
}
