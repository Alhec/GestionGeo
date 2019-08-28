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
         $this->call(UniversityTableSeeder::class);
        $this->call(FacultyTableSeeder::class);
        $this->call(OrganizationTableSeeder::class);
         $this->call(UsersTableSeeder::class);
         $this->call(OrganizationUserTableSeeder::class);
         $this->call(TeachersTableSeeder::class);
         $this->call(PostgraduatesTableSeeder::class);
         $this->call(StudentsTableSeeder::class);
         $this->call(SchoolPeriodsTableSeeder::class);
         $this->call(SubjectsTableSeeder::class);
         $this->call(SchoolPeriodSubjectTeacherTableSeeder::class);
         $this->call(PostgraduateSubjectTableSeeder::class);
         $this->call(SchoolPeriodStudentTableSeeder::class);
         $this->call(StudentSubjectTableSeeder::class);
         $this->call(SchedulesTableSeeder::class);
         Schema::enableForeignKeyConstraints();
    }
}
