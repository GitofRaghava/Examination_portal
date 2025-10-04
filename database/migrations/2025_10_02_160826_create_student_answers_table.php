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
        Schema::create('student_answers', function (Blueprint $table) {
             $table->id(); // bigIncrements = BIGINT UNSIGNED
    $table->unsignedBigInteger('exam_id');
    $table->unsignedBigInteger('question_id');
    $table->longText('chosen_option_ids')->nullable(); // so JSON arrays can fit
$table->longText('answer_text')->nullable();
    $table->timestamps();

    // Add foreign keys
    $table->unsignedBigInteger('student_id');
$table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
    $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_answers');
    }
};
