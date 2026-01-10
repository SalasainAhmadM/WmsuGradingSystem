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
        // Check if table exists first
        if (!Schema::hasTable('final_grades')) {
            Schema::create('final_grades', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('schedule_id');
                $table->unsignedBigInteger('student_id');
                $table->decimal('final_grade', 5, 2)->nullable();
                $table->enum('remarks', ['PASSED', 'FAILED', 'INC', 'DROP'])->nullable();
                $table->timestamps();
                
                // Add indexes
                $table->index(['schedule_id', 'student_id']);
                
                // Add foreign keys if needed
                // $table->foreign('schedule_id')->references('id')->on('schedulings')->onDelete('cascade');
                // $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            });
        } else {
            // If table exists but doesn't have remarks column, add it
            if (!Schema::hasColumn('final_grades', 'remarks')) {
                Schema::table('final_grades', function (Blueprint $table) {
                    $table->enum('remarks', ['PASSED', 'FAILED', 'INC', 'DROP'])->nullable()->after('final_grade');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('final_grades');
    }
};