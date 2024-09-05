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
        Schema::create('scheme_users', function(Blueprint $table){
            $table->index(['scheme_id', 'user_id']);

            $table->unsignedBigInteger('scheme_id');
            $table->unsignedBigInteger('user_id');

            // $table->foreign('scheme_id')->references('id')->on('schemes');
            // $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scheme_vendors');
    }
};
