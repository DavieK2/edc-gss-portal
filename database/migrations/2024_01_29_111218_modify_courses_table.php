<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('courses', function(Blueprint $table){
            Schema::disableForeignKeyConstraints();
            $table->foreignId('account_id')->change()->nullable()->references('id')->on('accounts');
        });
    }

   
    public function down()
    {
        //
    }
};
