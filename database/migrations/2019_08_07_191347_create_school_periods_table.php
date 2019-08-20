<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolPeriodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_periods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('cod_school_period')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->float('duty');
            $table->boolean('inscription_visible');
            $table->boolean('end_school_period');
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
        Schema::dropIfExists('school_periods');
        Schema::enableForeignKeyConstraints();
    }
}
