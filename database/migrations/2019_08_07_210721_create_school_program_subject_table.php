<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolProgramSubjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_program_subject', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('school_program_id');
            $table->unsignedBigInteger('subject_id');
            $table->string('type',2)
                ->nullable();
            $table->integer('subject_group')
                ->nullable();
            $table->foreign('school_program_id')
                ->references('id')
                ->on('school_programs')
                ->onDelete('cascade');
            $table->foreign('subject_id')
                ->references('id')
                ->on('subjects')
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
        Schema::dropIfExists('school_program_subject');
        Schema::enableForeignKeyConstraints();
    }
}
