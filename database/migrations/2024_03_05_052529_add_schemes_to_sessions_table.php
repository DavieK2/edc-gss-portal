<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('sessions', function (Blueprint $table) {
            $table->dropColumn('can_register');
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->json('can_register')->nullable();
        });
    }
};
