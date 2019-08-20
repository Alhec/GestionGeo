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
            'user_type' => 'A',  //S T A
        ]);
        User::create([
            'identification' => '24698917',
            'first_name' => 'Luis',
            'first_surname' => 'Hubrea',
            'mobile' => '(1234) 567-8901',
            'email' => 'luis@teacher.com',
            'password' => Hash::make('24698917'),
            'user_type' => 'T',
        ]);
        User::create([
            'identification' => '24698918',
            'first_name' => 'Abraham',
            'first_surname' => 'Navarro',
            'mobile' => '(1234) 567-8901',
            'email' => 'abraham@student.com',
            'password' => Hash::make('24698918'),
            'user_type' => 'S',
        ]);

    }
}
