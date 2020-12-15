<?php

use Illuminate\Database\Seeder;
use App\Roles;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Roles::query()->truncate();
        Roles::create([
            'id'=>1,
            'user_type'=>'A'
        ]);
        Roles::create([
            'id'=>2,
            'user_type'=>'A'
        ]);
    }
}
