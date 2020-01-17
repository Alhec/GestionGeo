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
            $table->string('organization_id',10);
            $table->string('cod_school_period',10);
            $table->date('start_date');
            $table->date('end_date');
            $table->date('withdrawal_deadline');
            $table->boolean('load_notes')
                ->default(false);
            $table->date('inscription_start_date');
            $table->boolean('inscription_visible')
                ->default(false);
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
        Schema::dropIfExists('school_periods');
        Schema::enableForeignKeyConstraints();
    }
}
