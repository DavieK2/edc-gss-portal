<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('session_ventures', function(Blueprint $table){
            $table->json('registration_type')->change();
        });
    }

  
    public function down()
    {
        //
    }
};
