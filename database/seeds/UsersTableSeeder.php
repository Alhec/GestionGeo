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
            'identification' => '24698917',
            'first_name' => 'Felix',
            'first_surname' => 'Urbano',
            'mobile' => '(1234) 567-8901',
            'email' => 'felix@admin.com',
            'password' => Hash::make('24698919'),
            'user_type' => 'A',  //S T A
            'active'=>true,
        ]);
        User::create([
            'identification' => '24698918',
            'first_name' => 'Luis',
            'first_surname' => 'Hubrea',
            'mobile' => '(1234) 567-8901',
            'email' => 'luis@teacher.com',
            'password' => Hash::make('24698918'),
            'user_type' => 'T',
            'active'=>true,
        ]);
        User::create([
            'identification' => '24698919',
            'first_name' => 'Abraham',
            'first_surname' => 'Navarro',
            'mobile' => '(1234) 567-8901',
            'email' => 'abraham@teacher.com',
            'password' => Hash::make('24698919'),
            'user_type' => 'T',
            'active'=>true,
        ]);
        User::create([
            'identification' => '24698920',
            'first_name' => 'Leonardo',
            'first_surname' => 'Navarro',
            'mobile' => '(1234) 567-8901',
            'email' => 'leonardo@teacher.com',
            'password' => Hash::make('24698920'),
            'user_type' => 'T',
            'active'=>true,
        ]);
        User::create([
            'identification' => '24698921',
            'first_name' => 'Felipe',
            'first_surname' => 'Alayon',
            'mobile' => '(1234) 567-8901',
            'email' => 'felipe@teacher.com',
            'password' => Hash::make('24698921'),
            'user_type' => 'T',
            'active'=>true,
        ]);
        User::create([
            'identification' => '24698922',
            'first_name' => 'Jean',
            'first_surname' => 'Trujillo',
            'mobile' => '(1234) 567-8901',
            'email' => 'jean@student.com',
            'password' => Hash::make('24698922'),
            'user_type' => 'S',  //S T A,
            'active'=>true,
        ]);
        User::create([
            'identification' => '24698923',
            'first_name' => 'Macgiver',
            'first_surname' => 'Bracho',
            'mobile' => '(1234) 567-8901',
            'email' => 'macgiver@student.com',
            'password' => Hash::make('24698923'),
            'user_type' => 'S',  //S T A
            'active'=>true,
        ]);
        User::create([
            'identification' => '24698924',
            'first_name' => 'Luis',
            'first_surname' => 'Ramos',
            'mobile' => '(1234) 567-8901',
            'email' => 'luis@student.com',
            'password' => Hash::make('24698924'),
            'user_type' => 'S',
            'active'=>true,
        ]);
        User::create([
            'identification' => '24698925',
            'first_name' => 'Elena',
            'first_surname' => 'Hubrea',
            'mobile' => '(1234) 567-8901',
            'email' => 'elena@student.com',
            'password' => Hash::make('24698925'),
            'user_type' => 'S',
            'active'=>true,
        ]);
        User::create([
            'identification' => '24698926',
            'first_name' => 'Gabriel',
            'first_surname' => 'Uribe',
            'mobile' => '(1234) 567-8901',
            'email' => 'gabriel@student.com',
            'password' => Hash::make('24698926'),
            'user_type' => 'S',
            'active'=>true,
        ]);
        User::create([
            'identification' => '24698927',
            'first_name' => 'Jose',
            'first_surname' => 'Ortiz',
            'mobile' => '(1234) 567-8901',
            'email' => 'jose@student.com',
            'password' => Hash::make('24698927'),
            'user_type' => 'S',
            'active'=>true,
        ]);
    }
}
