<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('postgraduate_id');
            $table->unsignedBigInteger('user_id');
            $table->string('student_type',3);
            $table->string('level_instruction',20);
            $table->string('home_university',70);
            $table->string('current_postgraduate',70)->nullable();
            $table->text('degrees')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('postgraduate_id')->references('id')->on('postgraduates')->onDelete('cascade');
            $table->timestamps();
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
        Schema::dropIfExists('password_resets');
        Schema::enableForeignKeyConstraints();
        Schema::dropIfExists('students');
    }
}
