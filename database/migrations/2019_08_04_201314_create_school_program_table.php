<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolProgramTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_programs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('organization_id',10);
            $table->string('school_program_name',100);
            $table->integer('num_cu')
                ->nullable();
            $table->integer('min_num_cu_final_work')
                ->default(8)
                ->nullable();
            $table->integer('duration')
                ->nullable();
            $table->integer('min_duration')
                ->default(2)
                ->nullable();
            $table->boolean('grant_certificate')
                ->default(false);
            $table->boolean('conducive_to_degree')
                ->default(true);
            $table->boolean('doctoral_exam')
                ->default(false);
            $table->integer('min_cu_to_doctoral_exam')
                ->nullable();
            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
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
        Schema::dropIfExists('school_programs');
        Schema::enableForeignKeyConstraints();

    }
}
