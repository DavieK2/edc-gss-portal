<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up()
    {
        Schema::table('schemes', function (Blueprint $table) {
            $table->string('payment_split_id')->nullable();
        });
    }

    public function down()
    {
        //
    }
};
