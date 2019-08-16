<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostgraduateSubjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('postgraduate_subject', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('postgraduate_id');
            $table->unsignedBigInteger('subject_id');
            $table->string('type',1);
            $table->timestamps();
            $table->foreign('postgraduate_id')->references('id')->on('postgraduates')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
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
        Schema::dropIfExists('postgraduate_subject');
        Schema::enableForeignKeyConstraints();
    }
}
