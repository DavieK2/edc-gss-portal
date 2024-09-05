<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up()
    {
        Schema::table('session_courses', function(Blueprint $table){
            $table->json('levels')->nullable();
        });
    }

   
    public function down()
    {
        //
    }
};
