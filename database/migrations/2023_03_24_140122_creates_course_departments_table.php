<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
       Schema::create('course_departments', function(Blueprint $table){
            $table->index(['course_id', 'department_id', 'semester_id']);
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('department_id'); 
            $table->json('semester_id'); 
            $table->json('levels'); 
        });
    }

 
    public function down()
    {
       Schema::dropIfExists('course_departments');
    }
};
