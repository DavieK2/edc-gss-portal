<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('session_courses', function(Blueprint $table){
            $table->foreignId('session_id')->references('id')->on('sessions');
            $table->foreignId('course_id')->references('id')->on('courses');
        });
    }

   
    public function down()
    {
        //
    }
};
