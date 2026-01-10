<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lab_lec_grades', function (Blueprint $table) {
            // Check if term_id column doesn't exist before adding it
            if (!Schema::hasColumn('lab_lec_grades', 'term_id')) {
                $table->unsignedBigInteger('term_id')->nullable()->after('schedule_id');
                $table->foreign('term_id')->references('id')->on('terms')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lab_lec_grades', function (Blueprint $table) {
            if (Schema::hasColumn('lab_lec_grades', 'term_id')) {
                $table->dropForeign(['term_id']);
                $table->dropColumn('term_id');
            }
        });
    }
};