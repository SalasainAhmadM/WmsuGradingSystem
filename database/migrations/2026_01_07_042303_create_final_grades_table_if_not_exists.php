<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if table exists first
        if (!Schema::hasTable('final_grades')) {
            // Create using raw SQL to match your database style
            DB::statement("
                CREATE TABLE IF NOT EXISTS final_grades (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    schedule_id INT NOT NULL,
                    student_id INT NOT NULL,
                    final_grade DECIMAL(5, 2) NULL,
                    remarks ENUM('PASSED', 'FAILED', 'INC', 'DROP') NULL,
                    date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
                    date_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_schedule_student (schedule_id, student_id)
                )
            ");
        } else {
            // If table exists but doesn't have remarks column, add it
            $columns = DB::select("SHOW COLUMNS FROM final_grades LIKE 'remarks'");
            if (empty($columns)) {
                DB::statement("
                    ALTER TABLE final_grades 
                    ADD COLUMN remarks ENUM('PASSED', 'FAILED', 'INC', 'DROP') NULL AFTER final_grade
                ");
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