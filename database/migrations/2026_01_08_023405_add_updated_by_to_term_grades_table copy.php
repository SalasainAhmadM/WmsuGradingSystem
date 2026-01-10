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
        Schema::table('term_grades', function (Blueprint $table) {
            // Check if the column doesn't exist before adding it
            if (!Schema::hasColumn('term_grades', 'remarks')) {
                $table->string('remarks', 20)->nullable()->after('other');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('term_grades', function (Blueprint $table) {
            if (Schema::hasColumn('term_grades', 'remarks')) {
                $table->dropColumn('remarks');
            }
        });
    }
};