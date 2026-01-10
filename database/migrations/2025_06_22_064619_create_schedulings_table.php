<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Find the up() method and change it to:
    public function up(): void
    {
        // Use raw query with IF NOT EXISTS
        DB::statement("
            CREATE TABLE IF NOT EXISTS schedulings(
                id INT PRIMARY KEY AUTO_INCREMENT,
                school_year_id INT,
                college_id INT,
                department_id INT,
                year_level_id INT,
                semester_id INT,
                schedule_id INT,
                subject_id INT,
                faculty_id INT,
                room_id INT ,
                schedule_from DATETIME,
                schedule_to DATETIME,
                day VARCHAR(255),
                is_lec BOOLEAN DEFAULT 1,
                date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
                date_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedulings');
    }
};
