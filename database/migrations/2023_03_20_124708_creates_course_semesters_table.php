<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  
    public function up()
    {
        Schema::create('course_semesters', function(Blueprint $table){
            $table->index(['course_id', 'semester_id']);
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('semester_id'); 
        });
    }

 
    public function down()
    {
       Schema::dropIfExists('course_semesters');
    }
};
