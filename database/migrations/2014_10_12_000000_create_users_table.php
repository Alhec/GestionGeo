<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('identification',20);
            $table->string('first_name',20);
            $table->string('second_name',20)
                ->nullable();
            $table->string('first_surname',20);
            $table->string('second_surname',20)
                ->nullable();
            $table->string('telephone',15)
                ->nullable();
            $table->string('mobile',15);
            $table->string('work_phone',15)
                ->nullable();
            $table->string('email',30);
            $table->timestamp('email_verified_at')
                ->nullable();
            $table->string('password',250);
            $table->string('user_type',1);
            $table->string('level_instruction',3);
            $table->boolean('active')
                ->default(true);
            $table->boolean('with_disabilities')
                ->default(false);
            $table->string('sex',1);
            $table->string('nationality',1);
            $table->string('organization_id',10);
            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
        Schema::enableForeignKeyConstraints();
    }
}
