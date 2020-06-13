<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code',10);
            $table->string('name',100);
            $table->integer('uc');
            $table->boolean('is_final_subject')
                ->default(false);
            $table->boolean('is_project_subject')
                ->default(false);
            $table->integer('theoretical_hours')
                ->default(0);
            $table->integer('practical_hours')
                ->default(0);
            $table->integer('laboratory_hours')
                ->default(0);
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
        Schema::dropIfExists('subjects');
        Schema::enableForeignKeyConstraints();
    }
}
