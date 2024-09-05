<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_users', function(Blueprint $table){
            $table->index(['role_id', 'user_id']);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('role_id');

            // $table->foreign('user_id')->references('id')->on('users');
            // $table->foreign('role_id')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
