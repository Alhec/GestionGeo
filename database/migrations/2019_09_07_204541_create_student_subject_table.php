<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentSubjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_subject', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('school_period_student_id');
            $table->unsignedBigInteger('school_period_subject_teacher_id');
            $table->integer('qualification')
                ->nullable();
            $table->string('status',3);
            $table->foreign('school_period_student_id')
                ->references('id')
                ->on('school_period_student')
                ->onDelete('cascade');
            $table->foreign('school_period_subject_teacher_id')
                ->references('id')
                ->on('school_period_subject_teacher')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('student_subject');
        Schema::enableForeignKeyConstraints();

    }
}
