<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('token_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->unsignedBigInteger('scheme_id');
            $table->string('reference')->nullable();
            $table->string('type');
            $table->boolean('payment_status')->default(false);
            $table->integer('number_of_tokens');
            $table->integer('amount');
            $table->timestamps();

            // $table->foreign('vendor_id')->references('id')->on('users');
            // $table->foreign('scheme_id')->references('id')->on('schemes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('token_transactions');
    }
};
