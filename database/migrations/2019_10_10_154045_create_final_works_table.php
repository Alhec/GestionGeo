<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinalWorksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('final_works', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title',100);
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('project_id')
                ->nullable();
            $table->string('status',10)
                ->default('progress');
            $table->boolean('is_project?')
                ->default(false);
            $table->integer('attempts')
                ->default(1);
            $table->text('description_status')
                ->nullable();
            $table->date('approval_date')
                ->nullable();
            $table->foreign('student_id')
                ->references('id')
                ->on('students')
                ->onDelete('cascade');
            $table->foreign('subject_id')
                ->references('id')
                ->on('subjects')
                ->onDelete('cascade');
            $table->foreign('project_id')
                ->references('id')
                ->on('final_works')
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
        Schema::dropIfExists('final_works');
    }
}
