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
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('fullname');
            $table->integer('session_id')->index();
            $table->string('mat_no')->nullable();
            $table->integer('faculty_id')->index();
            $table->integer('department_id')->index();
            $table->string('school_fees_pin');
            $table->string('student_code')->unique();
            $table->string('profile_image')->nullable();
            $table->string('biometric_data')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('level_id');
            $table->timestamps();


            // $table->foreign('user_id')->references('id')->on('users');
            // $table->foreign('level_id')->references('id')->on('levels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_profiles');
    }
};
