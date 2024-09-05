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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('item_code');
            $table->integer('fee');
            $table->string('documentation_title')->nullable();
            $table->string('documentation_item_code')->nullable();
            $table->integer('documentation_fee')->nullable();
            $table->integer('documentation_fee_account_id')->nullable();
            $table->integer('carryover_id')->nullable();
            $table->boolean('is_venture')->default(false);
            $table->boolean('requires_venture')->default(false);
            $table->integer('max_registrations')->nullable();
            $table->unsignedBigInteger('account_id');
            $table->timestamps();

            // $table->foreign('account_id')->references('id')->on('accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courses');
    }
};
