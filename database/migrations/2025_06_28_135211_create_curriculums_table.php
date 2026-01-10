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
        DB::statement("
            CREATE TABLE IF NOT EXISTS curriculums(
                id INT PRIMARY KEY AUTO_INCREMENT,
                school_year_id INT,
                college_id INT,
                department_id INT,
                prospectus TEXT,
                effective_date DATE,
                is_editable BOOLEAN DEFAULT 1,
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
        Schema::dropIfExists('curriculums');
    }
};
