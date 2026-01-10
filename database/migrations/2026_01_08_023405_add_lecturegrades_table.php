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
        // Only create table if it doesn't exist
        if (!Schema::hasTable('final_grades')) {
            Schema::create('final_grades', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('schedule_id');
                $table->unsignedBigInteger('student_id');
                $table->decimal('lecture_grade', 8, 2)->nullable();
                $table->decimal('laboratory_grade', 8, 2)->nullable();
                $table->decimal('total_grade', 8, 2)->nullable();
                $table->decimal('weighted_grade', 8, 2)->nullable();
                $table->string('remarks', 50)->nullable();
                $table->timestamps();

                // Foreign keys
                $table->foreign('schedule_id')->references('id')->on('schedulings')->onDelete('cascade');
                $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');

                // Unique constraint
                $table->unique(['schedule_id', 'student_id']);

                // Indexes
                $table->index('schedule_id');
                $table->index('student_id');
            });
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