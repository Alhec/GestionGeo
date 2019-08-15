<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         Schema::disableForeignKeyConstraints();
         $this->call(UsersTableSeeder::class);
         //$this->call(AdministratorsTableSeeder::class);
         $this->call(TeachersTableSeeder::class);
         $this->call(PostgraduatesTableSeeder::class);
         $this->call(StudentsTableSeeder::class);
        // $this->call(DegreesTableSeeder::class);
         $this->call(SchoolPeriodsTableSeeder::class);
         $this->call(SubjectsTableSeeder::class);
         $this->call(SchoolPeriodsSubjectsTeachersTableSeeder::class);
         $this->call(PostgraduateSubjectTableSeeder::class);
         $this->call(SchoolPeriodsStudentsTableSeeder::class);
         $this->call(StudentsSubjectsTableSeeder::class);
         $this->call(SchedulesTableSeeder::class);
         Schema::enableForeignKeyConstraints();
    }
}
