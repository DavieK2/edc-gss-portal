<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up()
    {
        Schema::table('courses', function(Blueprint $table){
            $table->boolean('has_split_accounts')->default(false);
            $table->json('account_ids')->nullable();
        });
    }

   
    public function down()
    {
        //
    }
};
