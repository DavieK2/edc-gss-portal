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
        Schema::create('course_registrations', function(Blueprint $table){

            $table->index(['course_id', 'registration_id']);

            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('registration_id');

            // $table->foreign('course_id')->references('id')->on('courses');
            // $table->foreign('registration_id')->references('id')->on('registrations');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_registrations');
    }
};
