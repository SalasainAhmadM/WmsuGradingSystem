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
        DB::statement('CREATE TABLE schedules(
            id INT PRIMARY KEY AUTO_INCREMENT,
            subject_id INT,
            faculty_id INT,
            room_id INT ,
            code VARCHAR(100),
            schedule_from DATETIME,
	        schedule_to DATETIME,
            day VARCHAR(255),
            is_lec BOOLEAN DEFAULT 1, 
            date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
            date_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
