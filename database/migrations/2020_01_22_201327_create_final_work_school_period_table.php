<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinalWorkSchoolPeriodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('final_work_school_period', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('status',10)
                ->default('progress');
            $table->text('description_status')
                ->nullable();
            $table->unsignedBigInteger('final_work_id');
            $table->unsignedBigInteger('school_period_student_id');
            $table->foreign('final_work_id')
                ->references('id')
                ->on('final_works')
                ->onDelete('cascade');
            $table->foreign('school_period_student_id')
                ->references('id')
                ->on('school_period_student')
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
        Schema::dropIfExists('final_work_school_period');
    }
}
