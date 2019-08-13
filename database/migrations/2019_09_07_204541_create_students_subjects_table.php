<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students_subjects', function (Blueprint $table) {
            $table->unsignedBigInteger('school_period_student_id');
            $table->unsignedBigInteger('school_period_subject_teacher_id');
            $table->integer('qualification')->nullable();
            $table->string('status',3);
            $table->timestamps();
            $table->foreign('school_period_student_id')->references('id')->on('school_periods_students')->onDelete('cascade');
            $table->foreign('school_period_subject_teacher_id')->references('id')->on('school_periods_subjects_teachers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students_subjects');
    }
}
