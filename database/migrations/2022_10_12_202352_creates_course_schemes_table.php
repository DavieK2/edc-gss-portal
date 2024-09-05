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
        Schema::create('course_schemes', function(Blueprint $table){
            $table->index(['course_id', 'scheme_id']);

            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('scheme_id');

            // $table->foreign('course_id')->references('id')->on('courses');
            // $table->foreign('scheme_id')->references('id')->on('schemes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_schemes');
    }
};
