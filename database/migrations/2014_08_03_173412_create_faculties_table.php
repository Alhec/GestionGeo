<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacultiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faculties', function (Blueprint $table) {
            $table->String('id',10)
                ->primary();
            $table->string('university_id',20);
            $table->string('name',100);
            $table->string('acronym',20)
                ->nullable();
            $table->foreign('university_id')
                ->references('id')
                ->on('universities')
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
        Schema::dropIfExists('faculties');
        Schema::enableForeignKeyConstraints();
    }
}
