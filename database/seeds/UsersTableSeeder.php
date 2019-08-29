<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        User::query()->truncate();
        User::create([
            'identification' => '24698916',
            'first_name' => 'Hector',
            'first_surname' => 'Alayon',
            'mobile' => '(1234) 567-8901',
            'email' => 'hector@admin.com',
            'password' => Hash::make('24698916'),
            'user_type' => 'A',  //S T A,
            'active'=>true,
        ]);
        User::create([
            'identification' => '24698919',
            'first_name' => 'Felix',
            'first_surname' => 'Urbano',
            'mobile' => '(1234) 567-8901',
            'email' => 'felix@admin.com',
            'password' => Hash::make('24698919'),
            'user_type' => 'A',  //S T A
            'active'=>true,
        ]);
        User::create([
            'identification' => '24698917',
            'first_name' => 'Luis',
            'first_surname' => 'Hubrea',
            'mobile' => '(1234) 567-8901',
            'email' => 'luis@teacher.com',
            'password' => Hash::make('24698917'),
            'user_type' => 'T',
            'active'=>true,
        ]);
        User::create([
            'identification' => '24698917',
            'first_name' => 'Juan',
            'first_surname' => 'Hubrea',
            'mobile' => '(1234) 567-8901',
            'email' => 'Juan@teacher.com',
            'password' => Hash::make('24698917'),
            'user_type' => 'T',
            'active'=>true,
        ]);
        User::create([
            'identification' => '24698918',
            'first_name' => 'Abraham',
            'first_surname' => 'Navarro',
            'mobile' => '(1234) 567-8901',
            'email' => 'abraham@student.com',
            'password' => Hash::make('24698918'),
            'user_type' => 'S',
            'active'=>true,
        ]);
        User::create([
            'identification' => '24698928',
            'first_name' => 'Felipe',
            'first_surname' => 'Navarro',
            'mobile' => '(1234) 567-8901',
            'email' => 'Felipe@student.com',
            'password' => Hash::make('24698928'),
            'user_type' => 'S',
            'active'=>true,
        ]);

    }
}
