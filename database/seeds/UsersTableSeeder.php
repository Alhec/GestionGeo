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
            'identification' => '12345678',
            'first_name' => 'Administrator',
            'first_surname' => 'Administrator',
            'mobile' => '(1234) 567-8901',
            'email' => 'administrator@admin.com',
            'password' => Hash::make('adminadmin'),
            'user_type' => 'A',  //S T A
        ]);
        User::create([
            'identification' => '22345678',
            'first_name' => 'Teacher',
            'first_surname' => 'Teacher',
            'mobile' => '(1234) 567-8901',
            'email' => 'teacher@admin.com',
            'password' => Hash::make('adminadmin'),
            'user_type' => 'T',
        ]);
        User::create([
            'identification' => '32345678',
            'first_name' => 'Student',
            'first_surname' => 'Student',
            'mobile' => '(1234) 567-8901',
            'email' => 'student@admin.com',
            'password' => Hash::make('adminadmin'),
            'user_type' => 'S',
        ]);

    }
}
