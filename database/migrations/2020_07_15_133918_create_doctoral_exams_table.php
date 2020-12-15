<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDoctoralExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doctoral_exams', function (Blueprint $table) {
            $table->unsignedBigInteger('school_period_student_id');
            $table->string('status',10);
            $table->timestamps();
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
        Schema::dropIfExists('doctoral_exams');
    }
}
