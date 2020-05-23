<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('school_program_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('guide_teacher_id')
                ->nullable();
            $table->string('student_type',3);
            $table->string('home_university',100);
            $table->string('current_postgraduate',100)
                ->nullable();
            $table->string('type_income',30)
                ->nullable();
            $table->boolean('is_ucv_teacher')
                ->default(false);
            $table->boolean('is_available_final_work')
                ->default(false);
            $table->boolean('repeat_approved_subject')
                ->default(false);
            $table->boolean('repeat_reprobated_subject')
                ->default(false);
            $table->integer('credits_granted')
                ->default(0)
                ->nullable();
            $table->boolean('with_work')
                ->default(false)
                ->nullable();
            $table->boolean('end_program')
                ->default(false);
            $table->boolean('test_period')
                ->default(false);
            $table->string('current_status',5)
                ->default('REG');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->foreign('guide_teacher_id')
                ->references('id')
                ->on('teachers')
                ->onDelete('cascade');
            $table->foreign('school_program_id')
                ->references('id')
                ->on('school_programs')
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
        Schema::dropIfExists('students');
        Schema::enableForeignKeyConstraints();
    }
}
