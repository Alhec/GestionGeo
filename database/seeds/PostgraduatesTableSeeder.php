<?php

use Illuminate\Database\Seeder;
use App\Postgraduate;

class PostgraduatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Postgraduate::query()->truncate();
        Postgraduate::create([
            'postgraduate_name' => 'Geoquimica',
            'num_cu' => 50,
        ]);
    }
}
