<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('school_period_subject_teacher_id');
            $table->string('day',10);
            $table->string('classroom',20);
            $table->time('start_hour');
            $table->time('end_hour');
            $table->timestamps();
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
        Schema::dropIfExists('schedules');
    }
}
