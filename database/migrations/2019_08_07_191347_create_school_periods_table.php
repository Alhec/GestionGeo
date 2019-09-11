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
            $table->string('organization_id');
            $table->string('cod_school_period',10);
            $table->date('start_date');
            $table->date('end_date');
            $table->date('withdrawal_deadline')->nullable();
            $table->boolean('load_notes');
            $table->boolean('inscription_visible');
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
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
