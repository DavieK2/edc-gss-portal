<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('department_faculties', function(Blueprint $table){
            $table->index(['faculty_id', 'department_id']);
            $table->unsignedBigInteger('faculty_id');
            $table->unsignedBigInteger('department_id'); 
        });
    }

 
    public function down()
    {
       Schema::dropIfExists('department_faculties');
    }
};
