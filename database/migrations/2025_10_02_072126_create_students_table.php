<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_id');
            $table->foreign('exam_id')
                  ->references('id')->on('exams')
                  ->onDelete('cascade');

            // Candidate details
            $table->string('candidate_name');
            $table->string('candidate_email')->index();
            $table->string('candidate_contact')->nullable();
            $table->string('candidate_city')->nullable();

            // OTP fields
            $table->string('otp')->nullable();
            $table->timestamp('otp_expires_at')->nullable();

            // Exam timings
           

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
