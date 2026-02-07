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
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_registration_id')->constrained('exam_registrations')->onDelete('cascade');
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('reading_score')->nullable();
            $table->integer('listening_score')->nullable();
            $table->integer('writing_score')->nullable();
            $table->integer('speaking_score')->nullable();
            $table->integer('total_score')->nullable();
            $table->string('level_achieved')->nullable(); // A1, A2, B1, B2, C1, C2
            $table->string('status')->default('pending'); // pending, passed, failed
            $table->dateTime('published_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_results');
    }
};
