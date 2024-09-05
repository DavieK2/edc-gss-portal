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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_profile_id')->nullable();
            $table->string('student_name');
            $table->string('reg_no');
            $table->string('level');
            $table->string('semester');
            $table->string('session');
            $table->string('department');
            $table->string('invoice_number')->unique();
            $table->string('temp_payment_ref')->nullable();
            $table->string('payment_ref')->nullable();
            $table->boolean('payment_status')->default(false);
            $table->json('items');
            $table->unsignedBigInteger('scheme_id');
            $table->unsignedBigInteger('faculty_id')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->integer('venture_id')->index()->nullable();
            $table->integer('registration_id')->index()->nullable();
            $table->boolean('is_verified')->default(false);
            $table->integer('verified_by')->index()->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();


            // $table->foreign('scheme_id')->references('id')->on('schemes');
            // $table->foreign('student_profile_id')->references('id')->on('student_profiles');
            // $table->foreign('vendor_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registrations');
    }
};
