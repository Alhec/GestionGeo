<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolPeriodStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_period_student', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('school_period_id');
            $table->string('status',5);
            $table->string('financing',3)
                ->nullable();//EXO exonerated, FUN Funded, SFI Self-financing, ScS Scholarship
            $table->text('financing_description')
                ->nullable();
            $table->string('pay_ref',50)
                ->nullable();
            $table->float('amount_paid')
                ->nullable();
            $table->date('inscription_date')
                ->default(now());
            $table->boolean('test_period')
                ->default(false)
                ->nullable();
            $table->foreign('student_id')
                ->references('id')
                ->on('students')
                ->onDelete('cascade');
            $table->foreign('school_period_id')
                ->references('id')
                ->on('school_periods')
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
        Schema::dropIfExists('school_period_student');
        Schema::enableForeignKeyConstraints();

    }
}
