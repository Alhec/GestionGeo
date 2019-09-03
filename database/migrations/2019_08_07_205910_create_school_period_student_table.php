<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolPeriodStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_period_student', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('school_period_id');
            $table->string('pay_ref')->nullable();
            $table->string('status',5);
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('school_period_id')->references('id')->on('school_periods')->onDelete('cascade');
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
        Schema::dropIfExists('school_period_student');
        Schema::enableForeignKeyConstraints();

    }
}
