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
        // Add exam_type to exams table
        Schema::table('exams', function (Blueprint $table) {
            $table->enum('exam_type', ['complete', 'module'])->default('complete')->after('description');
            $table->json('available_modules')->nullable()->comment('Array of available modules: ["reading", "writing", "listening", "speaking"]')->after('exam_type');
        });

        // Add selected_modules to exam_registrations table
        Schema::table('exam_registrations', function (Blueprint $table) {
            $table->json('selected_modules')->nullable()->comment('Array of modules selected by student')->after('amount_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_registrations', function (Blueprint $table) {
            $table->dropColumn('selected_modules');
        });

        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn(['exam_type', 'available_modules']);
        });
    }
};
