<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up()
    {
        Schema::create('session_ventures', function(Blueprint $table){
            $table->string('session')->index();
            $table->integer('course_id')->index();
            $table->integer('max_registrations')->default(300);
            $table->string('registration_type');
        });
    }

   
    public function down()
    {
        //
    }
};
