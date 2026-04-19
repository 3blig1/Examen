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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'sex')) {
                $table->string('sex', 20)->nullable()->after('birth_place');
            }
            if (!Schema::hasColumn('users', 'birth_country')) {
                $table->string('birth_country')->nullable()->after('sex');
            }
            if (!Schema::hasColumn('users', 'nationality')) {
                $table->string('nationality')->nullable()->after('birth_country');
            }
            if (!Schema::hasColumn('users', 'ssn')) {
                $table->string('ssn', 50)->nullable()->after('nationality');
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->string('address')->nullable()->after('ssn');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'address')) {
                $table->dropColumn('address');
            }
            if (Schema::hasColumn('users', 'ssn')) {
                $table->dropColumn('ssn');
            }
            if (Schema::hasColumn('users', 'nationality')) {
                $table->dropColumn('nationality');
            }
            if (Schema::hasColumn('users', 'birth_country')) {
                $table->dropColumn('birth_country');
            }
            if (Schema::hasColumn('users', 'sex')) {
                $table->dropColumn('sex');
            }
        });
    }
};
